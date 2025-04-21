// English: Handles RFID scanning and QR check-in integration
// Русский: Обрабатывает RFID и регистрацию по QR-коду

let rfidActive = true;
let qrTimeout;
let stream;
let qrScanLoop;

// English: Start RFID input focus loop
// Русский: Запуск фокуса ввода для RFID
function startRFID() {
  rfidActive = true;
  const input = document.getElementById("rfidInput");
  input.focus();

  const rfidInterval = setInterval(() => {
    if (!rfidActive) {
      clearInterval(rfidInterval);
    } else if (document.activeElement !== input) {
      input.focus();
    }
  }, 1000);
}

// English: Stop RFID listening
// Русский: Остановить прослушивание RFID
function stopRFID() {
  rfidActive = false;
}

// English: Start QR camera scanning for 30 seconds
// Русский: Запустить сканирование QR с камеры на 30 секунд
function startQRScan() {
  stopRFID();
  const qrArea = document.getElementById("qrVideoArea");

  navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then((mediaStream) => {
      stream = mediaStream;
      const video = document.createElement("video");
      video.srcObject = stream;
      video.setAttribute("playsinline", true);
      video.play();
      qrArea.innerHTML = "";
      qrArea.appendChild(video);

      const canvas = document.createElement("canvas");
      const context = canvas.getContext("2d");

      qrScanLoop = setInterval(() => {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
          canvas.width = video.videoWidth;
          canvas.height = video.videoHeight;
          context.drawImage(video, 0, 0, canvas.width, canvas.height);
          const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

          const code = jsQR(imageData.data, imageData.width, imageData.height);
          if (code) {
            const empId = code.data.trim();
            console.log("QR raw content:", empId);
            if (/^\d+$/.test(empId)) {
              console.log("✅ QR valid employee ID:", empId);
              stopQRScan();
              checkInWithID(empId);
            } else {
              console.warn("⚠️ Invalid QR content (not numeric):", code.data);
            }
          }
        }
      }, 500);

      qrTimeout = setTimeout(() => {
        stopQRScan();
        startRFID();
      }, 30000);
    })
    .catch((err) => {
      console.error("❌ Camera error:", err);
      startRFID();
    });
}

// English: Stop QR scanning and turn off camera
// Русский: Остановить сканирование QR и выключить камеру
function stopQRScan() {
  if (qrTimeout) clearTimeout(qrTimeout);
  if (qrScanLoop) clearInterval(qrScanLoop);
  const qrArea = document.getElementById("qrVideoArea");
  qrArea.innerHTML = "";
  if (stream) {
    stream.getTracks().forEach(track => track.stop());
  }
}

// English: Send ID from QR or RFID to server for check-in/out
// Русский: Отправить ID с QR или RFID на сервер для регистрации
function checkInWithID(empId) {
  fetch(`rfid_api.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `rfid_id=${encodeURIComponent(empId)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById("name").textContent = data.name;
        document.getElementById("status").textContent = data.status;
        document.getElementById("time").textContent = data.time;
        document.getElementById("message").textContent = "✅ Success";
      } else {
        document.getElementById("name").textContent = "";
        document.getElementById("status").textContent = "";
        document.getElementById("time").textContent = "";
        document.getElementById("message").textContent = "❌ " + (data.error || "Unknown error");
      }
      setTimeout(() => {
        document.getElementById("message").textContent = "Waiting for scan...";
        startRFID();
      }, 3000);
    })
    .catch(err => {
      console.error("❌ Server error:", err);
      document.getElementById("message").textContent = "❌ Server error";
      setTimeout(() => startRFID(), 3000);
    });
}

// English: Initialize listeners on page load
// Русский: Инициализация слушателей при загрузке страницы
window.addEventListener("DOMContentLoaded", () => {
  startRFID();

  // English: Listen for RFID input
  // Русский: Обработка ввода с RFID
  const input = document.getElementById("rfidInput");
  input.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && rfidActive) {
      const empId = input.value.trim();
      if (/^\d+$/.test(empId)) {
        console.log("📥 RFID read:", empId);
        input.value = "";
        checkInWithID(empId);
      } else {
        console.warn("⚠️ Invalid RFID format:", empId);
        input.value = "";
      }
    }
  });

  document.getElementById("qrBtn").addEventListener("click", startQRScan);
});