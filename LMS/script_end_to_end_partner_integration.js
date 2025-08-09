const interval = 2000;
// CONFIG
const CONFIG = {
  apiBaseUrl: "https://proj-ei-d-backend.vercel.app/api",
  callBackUrl: `https://glowing-puffpuff-2553fe.netlify.app/callback.html`,
  clientApp: `TestWebsite`,
  polling: {
    maxAttempts: 60,
    initialInterval: 3000,
    maxInterval: 10000,
  }
};

const mainbox = document.getElementById('login-card');

// Utility: Get dynamic user/device info: replace some deprectaed methods e.g platform, userAgent
async function getUserDeviceInfo() {
  const ipAddress = await getIPAddress();
  const deviceOS = await getDeviceOS();
  const location = await getDynamicLocation();
  const deviceId = getDeviceIdentifier(); // New!

  return {
    ipAddress,
    deviceInfo: {
      deviceModel: getDeviceModel(),
      deviceOS,
      deviceId, // New field
    },
    location
  };
}

// Generates or retrieves persistent device UUID
function getDeviceIdentifier() {
  const key = "device-uuid";
  let deviceId = localStorage.getItem(key);

  if (!deviceId) {
    // Generate securely using Web Crypto API
    deviceId = crypto.randomUUID();
    localStorage.setItem(key, deviceId);
  }

  return deviceId;
}

// IP, OS, model, location functions (same as previously provided)
async function getIPAddress() {
  try {
    const cached = sessionStorage.getItem("ipAddress");
    if (cached) return cached;
    const response = await fetch('https://api.ipify.org?format=json');
    const data = await response.json();
    sessionStorage.setItem("ipAddress", data.ip);
    return data.ip;
  } catch {
    return "0.0.0.0";
  }
}

async function getDeviceOS() {
  try {
    if (navigator.userAgentData?.getHighEntropyValues) {
      const { platform } = await navigator.userAgentData.getHighEntropyValues(['platform']);
      return platform;
    }
  } catch {}
  return detectOSLegacy();
}

function detectOSLegacy() {
  const ua = navigator.userAgent;
  if (/windows/i.test(ua)) return "Windows";
  if (/macintosh|mac os x/i.test(ua)) return "MacOS";
  if (/linux/i.test(ua)) return "Linux";
  if (/android/i.test(ua)) return "Android";
  if (/ios|iphone|ipad|ipod/i.test(ua)) return "iOS";
  return "Unknown";
}

function getDeviceModel() {
  return navigator.userAgent || "Unknown";
}

// Optional based on User consent
async function getDynamicLocation() {
  return new Promise((resolve) => {
    if (!navigator.geolocation) {
      return resolve({ latitude: null, longitude: null, error: "Geolocation unsupported" });
    }

    navigator.geolocation.getCurrentPosition(
      (pos) => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude }),
      async (err) => {
        console.warn("Geolocation failed:", err.message);

        // Fallback to IP-based location if needed
        const approxLocation = await getLocationFromIP();
        resolve({ ...approxLocation, error: err.message });
      },
      { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
    );
  });
}

// IP-based geolocation fallback (use a reliable provider or your backend)
async function getLocationFromIP() {
  try {
    const res = await fetch("https://ipapi.co/json/"); // OR your own server endpoint
    const data = await res.json();
    return {
      latitude: data.latitude || null,
      longitude: data.longitude || null,
      source: "IP-based"
    };
  } catch (e) {
    return { latitude: null, longitude: null, error: "IP-based fallback failed" };
  }
}

// Initiate Authentication
async function initiateAuthentication() {
  try {
    const userInfo = await getUserDeviceInfo();
    console.log("userInfo:", userInfo);

    const response = await fetch(`${CONFIG.apiBaseUrl}/authenticate`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json"
      },
      body: JSON.stringify({
        ...userInfo,
        callBackUrl: CONFIG.callBackUrl,
        clientApp: CONFIG.clientApp
      }),
    });

    // Check if the response is valid before parsing
    const rawText = await response.text();
    console.log("Raw response text:", rawText);

    if (!response.ok) {
      throw new Error(`Server returned ${response.status}: ${rawText}`);
    }

    const result = JSON.parse(rawText); // Now safe to parse
    return result.data;
  } catch (err) {
    console.error("Error initiating authentication:", err);

    alert("Authentication failed. See console for details.");
  }
}

// Poll Authentication Status
async function pollAuthenticationStatus(orderId) {
  let attempts = 0;
  let delay = CONFIG.polling.initialInterval;

  async function poll() {
    attempts++;
    try {
      const userInfo = await getUserDeviceInfo();
      const response = await fetch(`${CONFIG.apiBaseUrl}/auth/status`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          orderId,
          deviceInfo: userInfo.deviceInfo,
          ipAddress: userInfo.ipAddress
        })
      });

      if (!response.ok) throw new Error("Status fetch failed");

      const result = await response.json();
      const status = result?.data?.status;

      if (status === "Scanned") {
        showPinPromptUI();
        startPhoneAnimation();
        setTimeout(poll, delay);
      } else if (status === "Completed") {
        showSuccessUI();
        await new Promise(res => setTimeout(res, 1500));
        renderUserDashboardPage(result.data.user);
      } else if (attempts < CONFIG.polling.maxAttempts) {
        delay = Math.min(delay * 1.25, CONFIG.polling.maxInterval);
        setTimeout(poll, delay);
      } else {
        alert("Authentication still pending.");
      }
    } catch (err) {
      console.error("Polling error:", err);
      if (attempts < CONFIG.polling.maxAttempts) {
        delay = Math.min(delay * 1.5, CONFIG.polling.maxInterval);
        setTimeout(poll, delay);
      }
    }
  }

  poll();
}

// UI Helpers
function showPinPromptUI() {
  mainbox.innerHTML = `
    <div id="animation-container"></div>
    <h2>Enter security PIN in MyID app</h2>
    <div class="pin-status">
        <div class="pin-digits">
            ${Array(4).fill('<div class="pin-digit"></div>').join('')}
        </div>
        <p class="status-text">Verifying PIN<span class="ellipsis"></span></p>
    </div>`;
}

function startPhoneAnimation() {
  const animationContainer = document.getElementById("animation-container");
  if (animationContainer) {
    animationContainer.innerHTML = `
            <div class="phone-verification">
                <svg class="animated-phone" viewBox="0 0 24 24">
                    <rect class="phone-body" x="4" y="1" width="16" height="22" rx="2" fill="#4285F4"/>
                    <rect class="phone-screen" x="5" y="3" width="14" height="16" rx="1" fill="#fff"/>
                    <clipPath id="screen-clip">
                        <rect x="5" y="3" width="14" height="16" rx="1"/>
                    </clipPath>
                    <g id="digit-group" clip-path="url(#screen-clip)"></g>
                    <circle class="phone-button" cx="12" cy="20" r="1"/>
                </svg>
            </div>
        `;

    const group = document.getElementById("digit-group");

    function showRandomSequence() {
      const randomNumber = String(Math.floor(100000 + Math.random() * 900000));
      group.innerHTML = '';

      const safeMarginX = 6.5; // leave 1.5 units margin on sides
      const safeMarginY = 4.5; // leave 1.5 units margin top/bottom
      const width = 11; // usable width inside safe margins
      const height = 11; // usable height inside safe margins

      const positions = [];
      const gridSize = 3; // 3 cols, 2 rows grid => 6 cells

      const cellWidth = width / 3;
      const cellHeight = height / 2;

      const cells = [];
      for (let i = 0; i < 6; i++) cells.push(i);
      // shuffle to randomize cell placement
      for (let i = cells.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [cells[i], cells[j]] = [cells[j], cells[i]];
      }

      for (let i = 0; i < 6; i++) {
        const cell = cells[i];
        const col = cell % 3;
        const row = Math.floor(cell / 3);

        const posX = safeMarginX + col * cellWidth + cellWidth / 2;
        const posY = safeMarginY + row * cellHeight + cellHeight / 2;

        positions.push({ x: posX, y: posY });
      }

      let index = 0;

      function dropNextDigit() {
        if (index < randomNumber.length) {
          const digit = randomNumber[index];
          const target = positions[index];
          const text = document.createElementNS("http://www.w3.org/2000/svg", "text");
          text.textContent = digit;
          text.setAttribute("font-size", "3");
          text.setAttribute("text-anchor", "middle");
          text.setAttribute("fill", "#34A853");
          group.appendChild(text);

          const angle = Math.random() * 2 * Math.PI;
          const radius = 10;
          const startX = 12 + radius * Math.cos(angle);
          const startY = 10 + radius * Math.sin(angle);

          text.setAttribute("x", startX);
          text.setAttribute("y", startY);
          text.style.opacity = 0;

          text.animate([
            { transform: `translate(${startX - target.x}px, ${startY - target.y}px)`, opacity: 0 },
            { transform: `translate(0, 0)`, opacity: 1 }
          ], {
            duration: 500,
            easing: "ease-out",
            fill: "forwards"
          }).onfinish = () => {
            text.setAttribute("x", target.x);
            text.setAttribute("y", target.y);
            text.style.opacity = 1;
          };

          index++;
          setTimeout(dropNextDigit, 500);
        } else {
          setTimeout(showRandomSequence, interval);
        }
      }

      dropNextDigit();
    }

    showRandomSequence();
  }
}


function showSuccessUI() {
  mainbox.innerHTML = `
    <h2>Security Verified!</h2>
    <div class="success-message">
      <svg viewBox="0 0 24 24" width="48" height="48">
        <path fill="#34A853" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
      </svg>
      <p>Redirecting to your account...</p>
    </div>`;
}

// Render User Dashboard
function renderUserDashboardPage(userDetails) {
  // Store user data in sessionStorage
  localStorage.setItem('userDetails', JSON.stringify(userDetails));
  // Optional: Store the timestamp for timeout check (in case you want it)
  localStorage.setItem('userDetailsTimestamp', Date.now());
  // Redirect to user.html
  window.location.href = 'home.php';
}

// Login Methods
async function loginWithMyIDOnSameDevice() {
  const authData = await initiateAuthentication();
  if (!authData) return;

  pollAuthenticationStatus(authData.orderID);

  const deepLinkUrl = `myapp://identify?callback_url=${CONFIG.callBackUrl}&token=${authData.deepLinkToken}`;
  window.open(deepLinkUrl, "_blank");
}

async function generateQRCodeForAnotherDevice() {
  const authData = await initiateAuthentication();
  if (!authData) return;

  pollAuthenticationStatus(authData.orderID);

  mainbox.innerHTML = `
    <h2>Scan this QR Code with MyID on your mobile</h2>
    <div id="qr-code-container"></div>
    <p>This QR code updates every second and expires in 3 minutes.</p>
  `;

  await renderMyIDMovingQRCode(authData.qrCodeToken, authData.qrCodeSecret);
}

// HMAC + QR code remains same (from previous code)


// Helper: Normalize Base64
function normalizeBase64(b64) {
  return b64.replace(/-/g, '+').replace(/_/g, '/').padEnd(Math.ceil(b64.length / 4) * 4, '=');
}

// Generate HMAC using secret + timestamp
async function generateHMAC(secretBase64, timestamp) {
  const secretBytes = Uint8Array.from(atob(normalizeBase64(secretBase64)), c => c.charCodeAt(0));
  const key = await crypto.subtle.importKey(
    'raw',
    secretBytes,
    { name: 'HMAC', hash: 'SHA-256' },
    false,
    ['sign']
  );
  const msgBuffer = new TextEncoder().encode(timestamp.toString());
  const hmacBuffer = await crypto.subtle.sign('HMAC', key, msgBuffer);
  return Array.from(new Uint8Array(hmacBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

// Render moving QR Code
async function renderMyIDMovingQRCode(qrcodeToken, qrcodeSecret) {
  const container = document.getElementById("qr-code-container");

  async function updateQR() {
    const timestamp = Math.floor(Date.now() / 1000);
    const hmac = await generateHMAC(qrcodeSecret, timestamp);
    const qrCodeData = `myapp://identify.${qrcodeToken}.${timestamp}.${hmac}`;

    console.log("QrLink", qrCodeData);

    const qr = qrcode(0, "M");
    qr.addData(qrCodeData);
    qr.make();

    if (container) {
      container.innerHTML = qr.createImgTag(4, 4);
    }
  }

  await updateQR();
  const intervalId = setInterval(updateQR, 1000);
  setTimeout(() => clearInterval(intervalId), 180000); // 3 min TTL
}

// Init Page
function renderLoginPage() {
  const urlParams = new URLSearchParams(window.location.search);
  const orderId = urlParams.get("orderID");

  if (orderId) {
    pollAuthenticationStatus(orderId);
  }
}

renderLoginPage();
