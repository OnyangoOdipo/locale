<?php
require_once '../config/database.php';
require_once '../components/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
}

// Get user data and their dialect from database
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT u.username, u.email, ud.dialect_id, d.name as dialect_name 
    FROM users u 
    LEFT JOIN user_dialects ud ON u.id = ud.user_id 
    LEFT JOIN dialects d ON ud.dialect_id = d.id 
    WHERE u.id = ?
    ORDER BY ud.created_at DESC
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Generate room ID based on dialect
$dialectId = $user['dialect_id'] ?? 1; // Default to 1 if no dialect selected
$roomId = "dialect_" . $dialectId . "_" . date("Ymd"); // Format: dialect_1_20240319
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Exchange Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<div class="min-h-screen theme-bg-secondary">
    <!-- Main Content Container -->
    <div class="max-w-6xl mx-auto px-4 py-6">
        <!-- Session Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold theme-text-primary mb-2">Language Exchange Room</h1>
            <p class="theme-text-secondary">Practice speaking with other learners in real-time</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <!-- Room Controls -->
            <div class="p-6 border-b theme-border">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <!-- Mute Button -->
                        <button id="muteButton" class="flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors theme-text-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            Mute
                        </button>

                        <!-- Video Button -->
                        <button id="videoButton" class="flex items-center px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors theme-text-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Video
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Share Button -->
                        <button id="shareButton" class="flex items-center px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                            </svg>
                            Share Room
                        </button>

                        <!-- End Call Button -->
                        <button id="endCallButton" class="flex items-center px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                            </svg>
                            End Call
                        </button>
                    </div>
                </div>
            </div>

            <!-- Video Container -->
            <div class="bg-black aspect-video">
                <div id="jaas-container" class="w-full h-full"></div>
            </div>

            <!-- Room Info -->
            <div class="p-6 bg-gray-50 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-sm theme-text-secondary">Room Active</span>
                    </div>
                    <p class="text-sm theme-text-secondary">Room ID: <?php echo htmlspecialchars($roomId); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Keep your existing scripts -->
<script src='https://8x8.vc/vpaas-magic-cookie-c3063f9f4a29471cbfc87a2e7fdeb68b/external_api.js'></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const domain = "8x8.vc";
    const options = {
        roomName: "vpaas-magic-cookie-c3063f9f4a29471cbfc87a2e7fdeb68b/" + "<?php echo $roomId; ?>",
        parentNode: document.querySelector('#jaas-container'),
        width: '100%',
        height: '100%',
        configOverwrite: {
            prejoinPageEnabled: false,
            disableDeepLinking: true,
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            enableNoisyMicDetection: true,
            brandingRoomAlias: "<?php echo $user['dialect_name']; ?> Room"
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'fullscreen',
                'fodeviceselection', 'hangup', 'chat',
                'settings', 'raisehand', 'videoquality',
                'filmstrip', 'tileview'
            ],
            SETTINGS_SECTIONS: ['devices', 'language', 'profile'],
            SHOW_JITSI_WATERMARK: false,
            DISABLE_JOIN_LEAVE_NOTIFICATIONS: true,
            MOBILE_APP_PROMO: false,
        },
        userInfo: {
            displayName: "<?php echo htmlspecialchars($user['username']); ?>",
            email: "<?php echo htmlspecialchars($user['email']); ?>"
        }
    };

    const api = new JitsiMeetExternalAPI(domain, options);

    // Handle mute button
    document.getElementById('muteButton').addEventListener('click', () => {
        api.executeCommand('toggleAudio');
    });

    // Handle video button
    document.getElementById('videoButton').addEventListener('click', () => {
        api.executeCommand('toggleVideo');
    });

    // Handle end call
    document.getElementById('endCallButton').addEventListener('click', () => {
        api.executeCommand('hangup');
        window.location.href = '/dashboard';
    });

    // Handle share button
    document.getElementById('shareButton').addEventListener('click', () => {
        const roomLink = `${window.location.origin}/video/join/${encodeURIComponent("<?php echo $roomId; ?>")}`;
        navigator.clipboard.writeText(roomLink).then(() => {
            const alert = document.createElement('div');
            alert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            alert.textContent = 'Room link copied to clipboard! (<?php echo $user['dialect_name']; ?> Room)';
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
        });
    });

    // Event listeners
    api.addEventListener('participantJoined', (participant) => {
        console.log('Participant joined:', participant);
    });

    api.addEventListener('videoConferenceJoined', (conference) => {
        console.log('Joined conference:', conference);
    });

    api.addEventListener('videoConferenceLeft', () => {
        window.location.href = '/dashboard';
    });
});
</script>
</html>