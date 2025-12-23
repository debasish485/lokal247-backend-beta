<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Worker OTP Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 16px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 14px;
        }
        button {
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .message {
            margin-top: 12px;
            font-size: 13px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Worker OTP Login</h2>

    <input
        type="text"
        id="phone"
        placeholder="Enter 10-digit mobile number"
    >

    <button id="send-otp">Send OTP</button>

    <input
        type="text"
        id="otp"
        placeholder="Enter OTP"
    >

    <button id="verify-otp" disabled>Verify OTP</button>

    <!-- REQUIRED -->
    <div id="recaptcha-container"></div>

    <div class="message" id="message"></div>
</div>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-app.js";
    import {
        getAuth,
        RecaptchaVerifier,
        signInWithPhoneNumber
    } from "https://www.gstatic.com/firebasejs/12.2.1/firebase-auth.js";

    // ✅ FIREBASE CONFIG (YOUR CREDENTIALS)
    const firebaseConfig = {
        apiKey: "AIzaSyCFvpw_auOBDKpFa7UlBpo1sqVV4QQif6A",
        authDomain: "evantum-fea9e.firebaseapp.com",
        projectId: "evantum-fea9e",
        storageBucket: "evantum-fea9e.firebasestorage.app",
        messagingSenderId: "306798783231",
        appId: "1:306798783231:web:a6f5d6a8fbbf04d4277aca",
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    auth.languageCode = 'en';

    // Setup reCAPTCHA (correct v9+ syntax)
    window.recaptchaVerifier = new RecaptchaVerifier(
        auth,
        'recaptcha-container',
        {
            size: 'invisible'
        }
    );

    let confirmationResult = null;

    const phoneInput = document.getElementById('phone');
    const otpInput = document.getElementById('otp');
    const sendBtn = document.getElementById('send-otp');
    const verifyBtn = document.getElementById('verify-otp');
    const messageBox = document.getElementById('message');

    // Enable Verify button only when OTP is 6 digits
    otpInput.addEventListener('input', () => {
        otpInput.value = otpInput.value.replace(/\D/g, '').slice(0, 6);
        verifyBtn.disabled = otpInput.value.length !== 6;
    });

    // Send OTP
    sendBtn.addEventListener('click', async () => {
        let phone = phoneInput.value.trim();

        if (!/^[6-9]\d{9}$/.test(phone)) {
            messageBox.textContent = "❌ Enter a valid 10-digit Indian mobile number";
            return;
        }

        phone = "+91" + phone;
        messageBox.textContent = "⏳ Sending OTP...";

        try {
            confirmationResult = await signInWithPhoneNumber(
                auth,
                phone,
                window.recaptchaVerifier
            );

            messageBox.textContent = "✅ OTP sent to " + phone;
        } catch (error) {
            console.error(error);
            messageBox.textContent = "❌ " + error.message;
        }
    });

    // Verify OTP
    verifyBtn.addEventListener('click', async () => {
        messageBox.textContent = "⏳ Verifying OTP...";

        try {
            const result = await confirmationResult.confirm(otpInput.value);
            const user = result.user;

            // 🔐 GET FIREBASE ID TOKEN
            const firebaseToken = await user.getIdToken();

            messageBox.textContent =
                "✅ OTP verified\n\nFirebase ID Token:\n" +
                firebaseToken;

            // 🔁 SEND TOKEN TO LARAVEL (API)
            fetch("/api/worker/login/otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    firebase_token: firebaseToken
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log("Laravel response:", data);
            });

        } catch (error) {
            console.error(error);
            messageBox.textContent = "❌ OTP verification failed: " + error.message;
        }
    });
</script>

</body>
</html>
