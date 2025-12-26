<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoVoS Barcode Scanner - Enhanced</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            padding: 20px;
        }

        /* FIXED: Better scanner container layout */
        .scanner-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .scanner-title {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .status {
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        .status.ready {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status.scanning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Manual Input Section */
        .manual-input-section {
            background: #e8f5e8;
            border: 2px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .manual-input-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #155724;
            margin-bottom: 10px;
            text-align: center;
        }

        .input-group {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .manual-input {
            padding: 10px 15px;
            border: 2px solid #c3e6cb;
            border-radius: 6px;
            font-size: 1rem;
            flex: 1;
            min-width: 200px;
            max-width: 300px;
        }

        .manual-input:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
        }

        .controls {
            text-align: center;
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            flex: 0 1 auto;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .btn.stop {
            background: #dc3545;
        }

        .btn.stop:hover {
            background: #c82333;
        }

        .btn.manual {
            background: #28a745;
        }

        .btn.manual:hover {
            background: #218838;
        }

        .btn.history {
            background: #6f42c1;
        }

        .btn.history:hover {
            background: #5a32a3;
        }

        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        /* FIXED: Better scanner section layout */
        .scanner-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }

        .camera-container {
            position: relative;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #camera-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            background: #000;
        }

        .camera-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .camera-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .results-panel {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 20px;
            max-height: 350px;
            overflow-y: auto;
        }

        .results-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }

        .result-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .result-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .result-item.success {
            border-color: #28a745;
            background: #d4edda;
        }

        .result-item.error {
            border-color: #dc3545;
            background: #f8d7da;
        }

        .result-item.warning {
            border-color: #ffc107;
            background: #fff3cd;
        }

        .scanned-code {
            font-family: 'Courier New', monospace;
            font-size: 1rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .user-info-scanner {
            font-size: 0.85rem;
            line-height: 1.4;
            color: #666;
        }

        .user-info {
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .user-info strong {
            color: #495057;
        }

        .permission-notice {
            background: #cce5ff;
            border: 1px solid #99d6ff;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }

        .permission-notice h3 {
            color: #0066cc;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .permission-notice p {
            color: #004499;
            line-height: 1.4;
            font-size: 0.9rem;
        }

        .camera-info {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 12px;
            margin: 12px 0;
            font-size: 0.85rem;
        }

        .debug-section {
            margin-top: 20px;
            background: #e9ecef;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            padding: 15px;
        }

        .debug-title {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }

        .debug-info {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            color: #6c757d;
            white-space: pre-line;
            max-height: 150px;
            overflow-y: auto;
        }

        /* History Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #000;
        }

        .history-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .history-stats {
            font-size: 0.9rem;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .scanner-section {
                grid-template-columns: 1fr;
            }

            .scanner-container {
                padding: 15px;
            }

            .scanner-title {
                font-size: 1.5rem;
            }

            .input-group {
                flex-direction: column;
            }

            .manual-input {
                min-width: 100%;
            }

            .modal-content {
                margin: 10% auto;
                width: 95%;
            }
        }
    </style>
</head>

<body>
    <div class="scanner-container">

        <div class="permission-notice">
            <h3>📱 Camera Permission Required</h3>
            <p>This application needs camera access to scan barcodes. Please allow camera permission when prompted.<br>
                <strong>Note:</strong> HTTPS is required for camera access in most browsers.
            </p>
        </div>

        <!-- Manual Input Section -->
        <div class="manual-input-section">
            <div class="manual-input-title">🖊️ Manual Input</div>
            <div class="input-group">
                <input type="text" id="manual-input" class="manual-input" placeholder="Enter plate number or name..."
                    maxlength="20">
                <button class="btn manual" onclick="searchManual()">🔍 Search</button>
            </div>
        </div>

        <div class="camera-info" id="camera-info" style="display: none;">
            <strong>Camera Information:</strong>
            <div id="camera-details">Loading camera information...</div>
        </div>

        <div class="status" id="status">Click "Start Scanner" to begin or enter plate number manually</div>

        <div class="controls">
            <button class="btn" id="startBtn" onclick="startScanner()">📷 Start Scanner</button>
            <button class="btn stop" id="stopBtn" onclick="stopScanner()" disabled>⏹️ Stop Scanner</button>
            <button class="btn history" id="historyBtn" onclick="showHistory()">📋 History</button>
            <button class="btn" id="testBtn" onclick="testConnection()">🔧 Test Connection</button>
            <button class="btn" id="cameraBtn" onclick="testCamera()">📹 Test Camera</button>
        </div>

        <div class="scanner-section">
            <div class="camera-container">
                <video id="camera-video" autoplay muted playsinline></video>
                <canvas id="camera-canvas" style="display: none;"></canvas>
                <div class="camera-overlay" id="camera-overlay">
                    <div class="camera-icon">📹</div>
                    <div>Camera Feed</div>
                    <div style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">
                        Position barcode in camera view
                    </div>
                </div>
            </div>

            <div class="results-panel">
                <div class="results-title">📊 Recent Results</div>
                <div id="results-container">
                    <div class="result-item">
                        <div class="scanned-code">Waiting for scan...</div>
                        <div class="user-info">Start the scanner or enter plate number manually</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">🔧 Debug Information</div>
            <div class="debug-info" id="debug-info">System ready. Click start to begin scanning or use manual input.
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div id="historyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">📋 Scan History</h2>
                <span class="close" onclick="closeHistory()">&times;</span>
            </div>
            <div class="history-controls">
                <div class="history-stats" id="history-stats">Loading...</div>
                <button class="btn" onclick="clearHistory()">🗑️ Clear History</button>
            </div>
            <div id="history-container">
                <div class="result-item">
                    <div class="scanned-code">No history available</div>
                    <div class="user-info">Start scanning to build your history</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include QuaggaJS from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

    <script>
        let scannerActive = false;
        let scanResults = [];
        let currentStream = null;
        let scanHistory = JSON.parse(localStorage.getItem('kovos_scan_history') || '[]');

        function updateStatus(message, type = 'ready') {
            const statusEl = document.getElementById('status');
            const startBtn = document.getElementById('startBtn');
            const stopBtn = document.getElementById('stopBtn');

            if (statusEl) {
                statusEl.textContent = message;
                statusEl.className = `status ${type}`;
            }

            // Update button states
            if (type === 'scanning') {
                startBtn.disabled = true;
                stopBtn.disabled = false;
            } else {
                startBtn.disabled = false;
                stopBtn.disabled = true;
            }
        }

        function addDebugInfo(message) {
            const debugEl = document.getElementById('debug-info');
            if (debugEl) {
                const timestamp = new Date().toLocaleTimeString();
                const newMessage = `[${timestamp}] ${message}`;
                debugEl.textContent += '\n' + newMessage;
                debugEl.scrollTop = debugEl.scrollHeight;
                console.log(newMessage); // Also log to console for debugging
            }
        }

        function showCameraInfo(devices) {
            const cameraInfoEl = document.getElementById('camera-info');
            const cameraDetailsEl = document.getElementById('camera-details');

            if (devices && devices.length > 0) {
                let info = `Found ${devices.length} camera(s):\n`;
                devices.forEach((device, index) => {
                    info += `${index + 1}. ${device.label || 'Unknown Camera'} (${device.deviceId.substring(0, 8)}...)\n`;
                });
                cameraDetailsEl.textContent = info;
                cameraInfoEl.style.display = 'block';
            }
        }

        function saveToHistory(result) {
            // Add timestamp to result
            const historyItem = {
                ...result,
                timestamp: Date.now(),
                date: new Date().toISOString()
            };

            // Add to beginning of history
            scanHistory.unshift(historyItem);

            // Keep only last 100 items
            if (scanHistory.length > 100) {
                scanHistory = scanHistory.slice(0, 100);
            }

            // Save to localStorage
            localStorage.setItem('kovos_scan_history', JSON.stringify(scanHistory));
        }

        function addScanResult(code, userInfo = null, isError = false, isManual = false) {
            const container = document.getElementById('results-container');
            if (!container) return;

            const resultDiv = document.createElement('div');
            resultDiv.className = `result-item ${isError ? 'error' : (userInfo ? 'success' : 'warning')}`;

            const timestamp = new Date().toLocaleTimeString();
            const method = isManual ? '🖊️ Manual' : '📷 Scanned';

            let resultHTML = `
    <div class="scanned-code">${method}: ${code}</div>
    <div class="user-info">
        <strong>Time:</strong> ${timestamp}<br>
        `;

            // Prepare result object for history
            const resultObj = {
                code: code,
                method: isManual ? 'manual' : 'scan',
                success: !isError && userInfo,
                error: isError ? userInfo : null
            };

            if (isError) {
                resultHTML += `<strong>Status:</strong> ❌ Error - ${userInfo}
    </div>`;
                resultObj.error = userInfo;
            } else if (userInfo && typeof userInfo === 'object') {
                resultHTML += `
    <strong>Status:</strong> ✅ User Found<br>
    <strong>Name:</strong> ${userInfo.nama || 'N/A'}<br>
    <strong>IC:</strong> ${userInfo.no_kp || 'N/A'}<br>
    <strong>Vehicle:</strong> ${userInfo.kenderaan || 'N/A'}<br>
    <strong>Status:</strong> ${userInfo.status || 'N/A'}
    </div>`;
                resultObj.userInfo = userInfo;
            } else {
                resultHTML += `<strong>Status:</strong> ⚠️ No user found with this identifier</div>`;
            }

            resultDiv.innerHTML = resultHTML;

            // Add to beginning of results
            container.insertBefore(resultDiv, container.firstChild);

            // Keep only last 10 results in display
            while (container.children.length > 10) {
                container.removeChild(container.lastChild);
            }

            // Save to history
            saveToHistory(resultObj);
        }

        async function searchManual() {
            const input = document.getElementById('manual-input');
            const query = input.value.trim();

            if (!query) {
                addDebugInfo('Manual search: No input provided');
                updateStatus('Please enter a plate number or name', 'error');
                return;
            }

            addDebugInfo(`Manual search for: ${query}`);
            updateStatus('Searching manually...', 'scanning');

            try {
                const response = await fetch('check_barcode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `barcode=${encodeURIComponent(query)}`
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.text();
                addDebugInfo(`Manual search response: ${result}`);

                // Parse the response
                if (result.includes('User Found:')) {
                    // Extract user info from the response
                    const lines = result.split('\n');
                    const userInfo = {};

                    lines.forEach(line => {
                        if (line.includes('Nama:')) userInfo.nama = line.split('Nama: ')[1];
                        if (line.includes('IC:')) userInfo.no_kp = line.split('IC: ')[1];
                        if (line.includes('Kenderaan:')) userInfo.kenderaan = line.split('Kenderaan: ')[1];
                        if (line.includes('Status:')) userInfo.status = line.split('Status: ')[1];
                    });

                    addScanResult(query, userInfo, false, true);
                    updateStatus('Manual search completed successfully', 'ready');
                } else {
                    addScanResult(query, result, false, true);
                    updateStatus('Manual search completed - no match found', 'ready');
                }

                // Clear input
                input.value = '';

            } catch (error) {
                addDebugInfo(`Manual search error: ${error.message}`);
                addScanResult(query, `Search error: ${error.message}`, true, true);
                updateStatus('Manual search failed', 'error');
            }
        }

        // Allow Enter key to trigger manual search
        document.addEventListener('DOMContentLoaded', function () {
            const manualInput = document.getElementById('manual-input');
            if (manualInput) {
                manualInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        searchManual();
                    }
                });
            }
        });

        function showHistory() {
            const modal = document.getElementById('historyModal');
            const container = document.getElementById('history-container');
            const stats = document.getElementById('history-stats');

            // Update stats
            const totalScans = scanHistory.length;
            const successfulScans = scanHistory.filter(item => item.success || (item.userInfo && !item.error)).length;
            const manualScans = scanHistory.filter(item => item.method === 'manual').length;

            stats.textContent = `Total: ${totalScans} | Successful: ${successfulScans} | Manual: ${manualScans}`;

            // Clear and populate history
            container.innerHTML = '';

            if (scanHistory.length === 0) {
                container.innerHTML = `
    <div class="result-item">
        <div class="scanned-code">No history available</div>
        <div class="user-info">Start scanning to build your history</div>
    </div>
    `;
            } else {
                scanHistory.forEach((item, index) => {
                    const resultDiv = document.createElement('div');
                    const isError = item.error;
                    const isSuccess = item.userInfo && !item.error;

                    resultDiv.className = `result-item ${isError ? 'error' : (isSuccess ? 'success' : 'warning')}`;

                    const date = new Date(item.timestamp).toLocaleString();
                    const method = item.method === 'manual' ? '🖊️ Manual' : '📷 Scanned';

                    let resultHTML = `
    <div class="scanned-code">${method}: ${item.code}</div>
    <div class="user-info">
        <strong>Date:</strong> ${date}<br>
        `;

                    if (isError) {
                        resultHTML += `<strong>Status:</strong> ❌ Error - ${item.error}
    </div>`;
                    } else if (isSuccess) {
                        resultHTML += `
    <strong>Status:</strong> ✅ User Found<br>
    <strong>Name:</strong> ${item.userInfo.nama || 'N/A'}<br>
    <strong>IC:</strong> ${item.userInfo.no_kp || 'N/A'}<br>
    <strong>Vehicle:</strong> ${item.userInfo.kenderaan || 'N/A'}<br>
    <strong>Status:</strong> ${item.userInfo.status || 'N/A'}
    </div>`;
                    } else {
                        resultHTML += `<strong>Status:</strong> ⚠️ No user found</div>`;
                    }

                    resultDiv.innerHTML = resultHTML;
                    container.appendChild(resultDiv);
                });
            }

            modal.style.display = 'block';
        }

        function closeHistory() {
            document.getElementById('historyModal').style.display = 'none';
        }

        function clearHistory() {
            if (confirm('Are you sure you want to clear all scan history? This action cannot be undone.')) {
                scanHistory = [];
                localStorage.removeItem('kovos_scan_history');
                addDebugInfo('Scan history cleared');
                showHistory(); // Refresh the display
            }
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('historyModal');
            if (event.target === modal) {
                closeHistory();
            }
        }

        async function testCamera() {
            addDebugInfo('Testing camera access...');
            updateStatus('Testing camera...', 'scanning');

            try {
                // Get available cameras
                const devices = await navigator.mediaDevices.enumerateDevices();
                const videoDevices = devices.filter(device => device.kind === 'videoinput');

                addDebugInfo(`Found ${videoDevices.length} video devices`);
                showCameraInfo(videoDevices);

                // Try to get camera stream
                const constraints = {
                    video: {
                        facingMode: { ideal: 'environment' },
                        width: { ideal: 1280, min: 640 },
                        height: { ideal: 720, min: 480 }
                    }
                };

                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                const videoElement = document.getElementById('camera-video');
                const overlay = document.getElementById('camera-overlay');

                addDebugInfo('Camera stream obtained successfully');

                // Set up video element
                videoElement.srcObject = stream;
                videoElement.style.display = 'block';
                overlay.style.display = 'none';

                // Log video track info
                const videoTracks = stream.getVideoTracks();
                if (videoTracks.length > 0) {
                    const track = videoTracks[0];
                    const settings = track.getSettings();
                    addDebugInfo(`Video track: ${track.label}`);
                    addDebugInfo(`Resolution: ${settings.width}x${settings.height}`);
                    addDebugInfo(`Frame rate: ${settings.frameRate}`);
                }

                updateStatus('Camera test successful! Video should be visible.', 'ready');

                // Store stream for cleanup
                currentStream = stream;

                // Auto-stop after 10 seconds
                setTimeout(() => {
                    if (currentStream && !scannerActive) {
                        stopCameraTest();
                    }
                }, 10000);

            } catch (error) {
                addDebugInfo(`Camera test failed: ${error.name} - ${error.message}`);
                updateStatus(`Camera error: ${error.message}`, 'error');

                // Show specific error messages
                if (error.name === 'NotAllowedError') {
                    addDebugInfo('Camera permission denied. Please allow camera access.');
                } else if (error.name === 'NotFoundError') {
                    addDebugInfo('No camera found. Please connect a camera.');
                } else if (error.name === 'NotReadableError') {
                    addDebugInfo('Camera is already in use by another application.');
                }
            }
        }

        function stopCameraTest() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
                addDebugInfo('Camera test stream stopped');
            }

            const videoElement = document.getElementById('camera-video');
            const overlay = document.getElementById('camera-overlay');

            videoElement.srcObject = null;
            videoElement.style.display = 'none';
            overlay.style.display = 'block';
        }

        async function checkBarcodeWithServer(barcode) {
            addDebugInfo(`Sending barcode to server: ${barcode}`);

            try {
                const response = await fetch('check_barcode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `barcode=${encodeURIComponent(barcode)}`
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.text();
                addDebugInfo(`Server response: ${result}`);

                // Parse the response
                if (result.includes('User Found:')) {
                    // Extract user info from the response
                    const lines = result.split('\n');
                    const userInfo = {};

                    lines.forEach(line => {
                        if (line.includes('Nama:')) userInfo.nama = line.split('Nama: ')[1];
                        if (line.includes('IC:')) userInfo.no_kp = line.split('IC: ')[1];
                        if (line.includes('Kenderaan:')) userInfo.kenderaan = line.split('Kenderaan: ')[1];
                        if (line.includes('Status:')) userInfo.status = line.split('Status: ')[1];
                    });

                    addScanResult(barcode, userInfo);
                    return userInfo;
                } else {
                    addScanResult(barcode, result);
                    return null;
                }

            } catch (error) {
                addDebugInfo(`Server request failed: ${error.message}`);
                addScanResult(barcode, `Server error: ${error.message}`, true);
                return null;
            }
        }

        async function testConnection() {
            addDebugInfo('Testing server connection...');
            updateStatus('Testing connection...', 'scanning');

            try {
                const response = await fetch('check_barcode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'barcode=TEST_CONNECTION'
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.text();
                addDebugInfo(`Connection test response: ${result}`);
                updateStatus('Connection test successful', 'ready');

            } catch (error) {
                addDebugInfo(`Connection test failed: ${error.message}`);
                updateStatus(`Connection failed: ${error.message}`, 'error');
            }
        }

        async function startScanner() {
            if (scannerActive) {
                addDebugInfo('Scanner already active');
                return;
            }

            addDebugInfo('Starting barcode scanner...');
            updateStatus('Starting scanner...', 'scanning');

            try {
                // Check if QuaggaJS is loaded
                if (typeof Quagga === 'undefined') {
                    throw new Error('QuaggaJS library not loaded');
                }

                // Stop any existing camera test
                stopCameraTest();

                const videoElement = document.getElementById('camera-video');
                const overlay = document.getElementById('camera-overlay');

                // Initialize Quagga
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: videoElement,
                        constraints: {
                            width: { ideal: 1280, min: 640 },
                            height: { ideal: 720, min: 480 },
                            facingMode: "environment"
                        }
                    },
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    },
                    numOfWorkers: 2,
                    decoder: {
                        readers: [
                            "code_128_reader",
                            "ean_reader",
                            "ean_8_reader",
                            "code_39_reader",
                            "code_39_vin_reader",
                            "codabar_reader",
                            "upc_reader",
                            "upc_e_reader",
                            "i2of5_reader"
                        ]
                    },
                    locate: true
                }, function (err) {
                    if (err) {
                        addDebugInfo(`Quagga initialization error: ${err.message}`);
                        updateStatus(`Scanner error: ${err.message}`, 'error');
                        return;
                    }

                    addDebugInfo('Quagga initialized successfully');
                    Quagga.start();
                    scannerActive = true;

                    // Hide overlay and show video
                    overlay.style.display = 'none';
                    videoElement.style.display = 'block';

                    updateStatus('Scanner active - Point camera at barcode', 'scanning');
                });

                // Set up barcode detection
                Quagga.onDetected(function (result) {
                    const code = result.codeResult.code;
                    addDebugInfo(`Barcode detected: ${code}`);

                    // Prevent duplicate rapid scans
                    if (scanResults.includes(code)) {
                        addDebugInfo(`Duplicate barcode ignored: ${code}`);
                        return;
                    }

                    scanResults.push(code);

                    // Limit scan results array
                    if (scanResults.length > 50) {
                        scanResults = scanResults.slice(-25);
                    }

                    // Check barcode with server
                    checkBarcodeWithServer(code);
                });

                // Set up processing listener for debugging
                Quagga.onProcessed(function (result) {
                    const drawingCtx = Quagga.canvas.ctx.overlay;
                    const drawingCanvas = Quagga.canvas.dom.overlay;

                    if (result) {
                        if (result.boxes) {
                            drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")),
                                parseInt(drawingCanvas.getAttribute("height")));
                            result.boxes.filter(function (box) {
                                return box !== result.box;
                            }).forEach(function (box) {
                                Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                            });
                        }

                        if (result.box) {
                            Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
                        }

                        if (result.codeResult && result.codeResult.code) {
                            Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 });
                        }
                    }
                });

            } catch (error) {
                addDebugInfo(`Scanner startup error: ${error.message}`);
                updateStatus(`Failed to start scanner: ${error.message}`, 'error');
                scannerActive = false;
            }
        }

        function stopScanner() {
            if (!scannerActive) {
                addDebugInfo('Scanner not active');
                return;
            }

            addDebugInfo('Stopping scanner...');

            try {
                Quagga.stop();
                scannerActive = false;

                const videoElement = document.getElementById('camera-video');
                const overlay = document.getElementById('camera-overlay');

                // Show overlay and hide video
                overlay.style.display = 'block';
                videoElement.style.display = 'none';

                updateStatus('Scanner stopped', 'ready');
                addDebugInfo('Scanner stopped successfully');

            } catch (error) {
                addDebugInfo(`Error stopping scanner: ${error.message}`);
                updateStatus('Error stopping scanner', 'error');
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            addDebugInfo('KoVoS Barcode Scanner initialized');
            updateStatus('Ready - Click Start Scanner or use Manual Input', 'ready');

            // Check if we're on HTTPS (required for camera access)
            if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
                addDebugInfo('Warning: HTTPS required for camera access in production');
                updateStatus('Warning: HTTPS required for camera access', 'error');
            }

            // Check browser compatibility
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                addDebugInfo('Error: Browser does not support camera access');
                updateStatus('Browser does not support camera access', 'error');
            }
        });

    </script>
</body>

</html>