@props([
    'routePrefix' => 'personnel', // 'personnel', 'enseignant', 'eleve', 'parent'
    'conversations' => collect(),
    'groups' => [],
    'contacts' => [],
    'unreadTotal' => 0
])

<div class="chat-container glass-card rounded-2xl overflow-hidden border border-outline-variant/40 shadow-xl flex h-[calc(100vh-140px)] min-h-[580px] bg-white">
    <!-- Colonne gauche : Liste des conversations -->
    <div class="chat-sidebar w-full md:w-[340px] lg:w-[380px] border-r border-outline-variant/40 flex flex-col flex-shrink-0 bg-surface-container-lowest transition-all" id="chatSidebar">
        <!-- Header gauche -->
        <div class="p-4 border-b border-surface-subtle bg-surface-container-low/40 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">chat</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-sm font-bold text-on-surface">Discussions</h3>
                    <p class="text-[11px] text-text-muted">
                        <span id="unreadTotalBadge">{{ $unreadTotal }}</span> non lu(s)
                    </p>
                </div>
            </div>
            <button type="button" onclick="openNewChatModal()" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white hover:opacity-90 active:scale-95 transition-all shadow-sm" title="Nouvelle discussion">
                <span class="material-symbols-outlined text-lg">add</span>
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="p-3 border-b border-surface-subtle bg-white">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-lg">search</span>
                <input type="text" id="conversationSearchInput" placeholder="Rechercher une discussion..." class="w-full pl-9 pr-3 py-1.5 text-xs border border-outline-variant rounded-lg bg-surface-container-low/30 focus:bg-white focus:ring-primary focus:border-primary">
            </div>
        </div>

        <!-- Liste des discussions -->
        <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-surface-subtle/50" id="conversationsList">
            @forelse($conversations as $conv)
                <div class="conversation-item p-3.5 flex items-start gap-3 cursor-pointer hover:bg-surface-container-low transition-colors relative {{ $loop->first ? '' : '' }}"
                     data-id="{{ $conv['id'] }}"
                     data-title="{{ strtolower($conv['title']) }}"
                     onclick="selectConversation({{ $conv['id'] }})">
                    
                    <!-- Avatar / Icône groupe -->
                    <div class="relative flex-shrink-0">
                        @if($conv['is_class_group'])
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-xl">groups</span>
                            </div>
                        @else
                            <div class="w-11 h-11 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-sm font-bold overflow-hidden border border-outline-variant/30">
                                @if($conv['avatar'])
                                    <img src="{{ $conv['avatar'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($conv['title'], 0, 2)) }}
                                @endif
                            </div>
                        @endif

                        @if($conv['unread_count'] > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                {{ $conv['unread_count'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Infos discussion -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <h4 class="text-xs font-semibold text-on-surface truncate">{{ $conv['title'] }}</h4>
                            <span class="text-[10px] text-text-muted whitespace-nowrap flex-shrink-0">{{ $conv['last_message_time'] }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <p class="text-[11px] text-text-muted truncate flex-1">{{ $conv['last_message'] ?: $conv['subtitle'] }}</p>
                            @if($conv['is_class_group'])
                                <span class="px-1.5 py-0.2 bg-secondary-container/60 text-secondary text-[9px] font-bold rounded uppercase">Groupe</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center" id="emptyConversationsNotice">
                    <div class="w-12 h-12 rounded-full bg-primary-fixed/40 text-primary flex items-center justify-center mx-auto mb-2.5">
                        <span class="material-symbols-outlined text-2xl">forum</span>
                    </div>
                    <p class="text-xs font-semibold text-on-surface">Aucune discussion</p>
                    <p class="text-[11px] text-text-muted mt-0.5">Cliquez sur « + » pour démarrer un échange avec un contact autorisé.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Colonne droite : Espace de discussion actif -->
    <div class="chat-main flex-1 flex flex-col bg-[#FAF9F6] relative" id="chatMain">
        <!-- État vide (aucune discussion sélectionnée) -->
        <div id="noChatSelected" class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-surface-container-lowest">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary/10 to-primary-container/20 text-primary flex items-center justify-center mb-4 shadow-sm">
                <span class="material-symbols-outlined text-4xl">mark_chat_unread</span>
            </div>
            <h3 class="text-base font-bold text-on-surface mb-1">Vos messages EduManager</h3>
            <p class="text-xs text-text-muted max-w-sm">Sélectionnez une discussion ou commencez un nouvel échange avec vos interlocuteurs autorisés.</p>
            <button type="button" onclick="openNewChatModal()" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg text-xs font-medium hover:opacity-90 transition-all shadow-sm inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">chat</span>
                Nouvelle conversation
            </button>
        </div>

        <!-- Chat actif (masqué par défaut) -->
        <div id="activeChat" class="hidden flex-1 flex flex-col h-full">
            <!-- Entête du chat -->
            <div class="chat-header px-4 py-3 border-b border-outline-variant/30 bg-white flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="backToSidebar()" class="md:hidden inline-flex items-center justify-center w-8 h-8 text-text-muted hover:text-on-surface">
                        <span class="material-symbols-outlined text-2xl">arrow_back</span>
                    </button>
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm overflow-hidden" id="activeChatAvatar">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-on-surface leading-tight" id="activeChatTitle">Discussion</h4>
                        <p class="text-[11px] text-text-muted leading-tight" id="activeChatSubtitle">Enseignant</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="refreshMessages()" class="w-8 h-8 rounded-lg text-text-muted hover:bg-surface-container flex items-center justify-center transition-colors" title="Actualiser">
                        <span class="material-symbols-outlined text-lg">refresh</span>
                    </button>
                </div>
            </div>

            <!-- Zone de défilement des messages -->
            <div class="messages-body flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-[#F8FAFC]" id="messagesBody">
                <!-- Les bulles de messages seront injectées dynamiquement ici -->
            </div>

            <!-- Barre d'enregistrement vocal active -->
            <div id="voiceRecordingBar" class="hidden px-4 py-3 bg-red-50 border-t border-red-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                    <span class="text-xs font-bold text-red-700">Enregistrement vocal en cours...</span>
                    <span class="font-mono text-xs text-red-900 font-bold ml-2" id="recordingTimer">00:00</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="cancelVoiceRecording()" class="px-3 py-1.5 text-xs text-red-700 hover:bg-red-100 rounded-lg font-medium">
                        Annuler
                    </button>
                    <button type="button" onclick="stopAndSendVoiceRecording()" class="px-3.5 py-1.5 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-sm">send</span>
                        Envoyer
                    </button>
                </div>
            </div>

            <!-- Aperçu de fichier sélectionné -->
            <div id="filePreviewBar" class="hidden px-4 py-2 bg-surface-container-low border-t border-outline-variant/30 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-on-surface truncate">
                    <span class="material-symbols-outlined text-primary text-base" id="filePreviewIcon">attach_file</span>
                    <span class="truncate font-medium" id="filePreviewName">fichier.pdf</span>
                </div>
                <button type="button" onclick="clearSelectedFile()" class="text-text-muted hover:text-alert-red">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>

            <!-- Barre de saisie inférieure (WhatsApp style) -->
            <div class="chat-footer p-3 bg-white border-t border-outline-variant/30 flex items-end gap-2">
                <!-- Bouton pièce jointe -->
                <label class="inline-flex items-center justify-center w-9 h-9 rounded-full text-text-muted hover:text-primary hover:bg-surface-container cursor-pointer transition-colors flex-shrink-0" title="Ajouter une photo, vidéo ou fichier">
                    <span class="material-symbols-outlined text-xl">attach_file</span>
                    <input type="file" id="chatFileInput" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" onchange="handleFileSelected(this)">
                </label>

                <!-- Bouton note vocale -->
                <button type="button" onclick="startVoiceRecording()" class="inline-flex items-center justify-center w-9 h-9 rounded-full text-text-muted hover:text-primary hover:bg-surface-container transition-colors flex-shrink-0" title="Enregistrer une note vocale">
                    <span class="material-symbols-outlined text-xl">mic</span>
                </button>

                <!-- Zone de texte -->
                <div class="flex-1 min-h-[38px] max-h-32 bg-surface-container-low/40 rounded-2xl border border-outline-variant/40 focus-within:border-primary focus-within:bg-white transition-all px-3 py-1.5 flex items-center">
                    <textarea id="messageTextInput" rows="1" placeholder="Écrivez un message..." class="w-full bg-transparent border-0 outline-none text-xs text-on-surface resize-none max-h-28 placeholder:text-text-muted" onkeydown="handleKeypress(event)" oninput="autoResizeTextarea(this)"></textarea>
                </div>

                <!-- Bouton envoyer -->
                <button type="button" onclick="sendMessage()" id="sendMessageBtn" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-primary text-white hover:opacity-90 active:scale-95 transition-all shadow-md flex-shrink-0" title="Envoyer">
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Conversation (Liste des contacts et groupes autorisés) -->
<div class="fixed inset-0 z-[200] hidden items-center justify-center p-4" id="newChatModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeNewChatModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all max-h-[85vh] flex flex-col">
        <div class="px-5 py-4 border-b border-surface-subtle bg-surface-container-low/40 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">chat_add_on</span>
                <h3 class="font-headline-sm text-sm font-bold text-on-surface">Nouvelle discussion</h3>
            </div>
            <button type="button" onclick="closeNewChatModal()" class="text-text-muted hover:text-on-surface">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="p-4 border-b border-surface-subtle">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-lg">search</span>
                <input type="text" id="contactFilterInput" placeholder="Rechercher un contact..." class="w-full pl-9 pr-3 py-2 text-xs border border-outline-variant rounded-lg bg-surface-container-low/20 focus:bg-white focus:ring-primary focus:border-primary">
            </div>
        </div>

        <div class="p-4 overflow-y-auto custom-scrollbar flex-1 space-y-4">
            <!-- Groupes autorisés (si applicable) -->
            @if(!empty($groups))
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-text-muted mb-2">Groupes de classe</h4>
                <div class="space-y-1">
                    @foreach($groups as $grp)
                        <div class="p-2.5 rounded-xl hover:bg-surface-container cursor-pointer flex items-center gap-3 transition-colors border border-outline-variant/30"
                             onclick="selectConversation({{ $grp['conversation_id'] }}); closeNewChatModal();">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-xl">groups</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-semibold text-on-surface truncate">{{ $grp['name'] }}</h5>
                                <p class="text-[11px] text-text-muted">{{ $grp['level'] ?? 'Groupe de classe' }}</p>
                            </div>
                            <span class="material-symbols-outlined text-text-muted text-base">chevron_right</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Contacts autorisés -->
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-text-muted mb-2">Contacts individuels autorisés</h4>
                @if(empty($contacts))
                    <p class="text-xs text-text-muted text-center py-4">Aucun contact individuel disponible selon vos permissions actuelles.</p>
                @else
                    <div class="space-y-1" id="contactsListContainer">
                        @foreach($contacts as $cnt)
                            <div class="contact-card p-2.5 rounded-xl hover:bg-surface-container cursor-pointer flex items-center gap-3 transition-colors border border-outline-variant/20"
                                 data-name="{{ strtolower($cnt['name']) }}"
                                 data-role="{{ strtolower($cnt['role']) }}"
                                 onclick="startDirectChat({{ $cnt['user_id'] }})">
                                <div class="w-9 h-9 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-xs font-bold overflow-hidden">
                                    @if($cnt['avatar'])
                                        <img src="{{ $cnt['avatar'] }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($cnt['name'], 0, 2)) }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-semibold text-on-surface truncate">{{ $cnt['name'] }}</h5>
                                    <p class="text-[11px] text-text-muted">{{ $cnt['role'] }} • {{ $cnt['email'] }}</p>
                                </div>
                                <span class="material-symbols-outlined text-primary text-base">send</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Visualisation Image (Lightbox) -->
<div class="fixed inset-0 z-[300] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-md" id="imageLightboxModal" onclick="this.classList.add('hidden'); this.classList.remove('flex');">
    <div class="relative max-w-4xl max-h-[90vh] overflow-hidden rounded-xl">
        <img id="lightboxImage" src="" alt="Aperçu image" class="max-w-full max-h-[85vh] object-contain rounded-lg">
    </div>
</div>

@push('scripts')
<script>
const routePrefix = @json($routePrefix);
let currentConversationId = null;
let lastMessageId = 0;
let pollingInterval = null;
let selectedFile = null;

// Audio recording variables
let mediaRecorder = null;
let audioChunks = [];
let recordingInterval = null;
let recordingSeconds = 0;

function openNewChatModal() {
    const m = document.getElementById('newChatModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeNewChatModal() {
    const m = document.getElementById('newChatModal');
    m.classList.remove('flex');
    m.classList.add('hidden');
}

function backToSidebar() {
    document.getElementById('chatSidebar').classList.remove('hidden');
    document.getElementById('chatSidebar').classList.add('w-full');
    document.getElementById('chatMain').classList.add('hidden');
}

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = (textarea.scrollHeight) + 'px';
}

function handleKeypress(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function openLightbox(url) {
    const modal = document.getElementById('imageLightboxModal');
    document.getElementById('lightboxImage').src = url;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
        document.getElementById('filePreviewName').textContent = selectedFile.name;
        
        let icon = 'attach_file';
        if (selectedFile.type.startsWith('image/')) icon = 'image';
        else if (selectedFile.type.startsWith('video/')) icon = 'movie';
        else if (selectedFile.type.startsWith('audio/')) icon = 'audiotrack';
        else if (selectedFile.type.includes('pdf')) icon = 'picture_as_pdf';
        
        document.getElementById('filePreviewIcon').textContent = icon;
        document.getElementById('filePreviewBar').classList.remove('hidden');
    }
}

function clearSelectedFile() {
    selectedFile = null;
    document.getElementById('chatFileInput').value = '';
    document.getElementById('filePreviewBar').classList.add('hidden');
}

// Enregistrement vocal
async function startVoiceRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];

        mediaRecorder.ondataavailable = event => {
            if (event.data.size > 0) audioChunks.push(event.data);
        };

        mediaRecorder.start();
        recordingSeconds = 0;
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('voiceRecordingBar').classList.remove('hidden');

        recordingInterval = setInterval(() => {
            recordingSeconds++;
            const mins = String(Math.floor(recordingSeconds / 60)).padStart(2, '0');
            const secs = String(recordingSeconds % 60).padStart(2, '0');
            document.getElementById('recordingTimer').textContent = `${mins}:${secs}`;
        }, 1000);
    } catch (err) {
        alert('Impossible d\'accéder au microphone. Veuillez autoriser l\'accès dans votre navigateur.');
    }
}

function cancelVoiceRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
    }
    clearInterval(recordingInterval);
    document.getElementById('voiceRecordingBar').classList.add('hidden');
    audioChunks = [];
}

function stopAndSendVoiceRecording() {
    if (!mediaRecorder || mediaRecorder.state === 'inactive') return;

    const duration = recordingSeconds;
    mediaRecorder.onstop = () => {
        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
        const audioFile = new File([audioBlob], `vocal_${Date.now()}.webm`, { type: 'audio/webm' });
        sendMediaDirectly(audioFile, 'audio', duration);
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
    };

    mediaRecorder.stop();
    clearInterval(recordingInterval);
    document.getElementById('voiceRecordingBar').classList.add('hidden');
}

function sendMediaDirectly(file, type, duration = null) {
    if (!currentConversationId) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);
    if (duration) formData.append('duration', duration);

    const baseUrl = `/${routePrefix}/messages/${currentConversationId}/send`;

    fetch(baseUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            appendMessage(res.message);
            lastMessageId = Math.max(lastMessageId, res.message.id);
            scrollToBottom();
            refreshConversationsList();
        }
    });
}

function startDirectChat(userId) {
    closeNewChatModal();
    const baseUrl = `/${routePrefix}/messages/start`;

    fetch(baseUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ recipient_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.conversation_id) {
            selectConversation(data.conversation_id);
            refreshConversationsList();
        }
    });
}

function selectConversation(convId) {
    currentConversationId = convId;
    lastMessageId = 0;

    // Sur mobile : basculer l'affichage
    if (window.innerWidth < 768) {
        document.getElementById('chatSidebar').classList.add('hidden');
        document.getElementById('chatMain').classList.remove('hidden');
    }

    document.getElementById('noChatSelected').classList.add('hidden');
    document.getElementById('activeChat').classList.remove('hidden');

    // Mettre en surbrillance l'item actif
    document.querySelectorAll('.conversation-item').forEach(el => {
        if (parseInt(el.dataset.id) === convId) {
            el.classList.add('bg-primary-fixed/20', 'border-l-4', 'border-primary');
        } else {
            el.classList.remove('bg-primary-fixed/20', 'border-l-4', 'border-primary');
        }
    });

    loadMessages(convId);

    // Déclencher le polling
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        if (currentConversationId) {
            pollNewMessages(currentConversationId);
        }
    }, 3000);
}

function loadMessages(convId) {
    const url = `/${routePrefix}/messages/${convId}/messages`;

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const conv = data.conversation;
        document.getElementById('activeChatTitle').textContent = conv.title;
        document.getElementById('activeChatSubtitle').textContent = conv.subtitle;
        
        const avatarEl = document.getElementById('activeChatAvatar');
        if (conv.is_class_group) {
            avatarEl.innerHTML = `<span class="material-symbols-outlined text-primary text-xl">groups</span>`;
        } else {
            avatarEl.innerHTML = `<span class="material-symbols-outlined text-primary text-xl">person</span>`;
        }

        const container = document.getElementById('messagesBody');
        container.innerHTML = '';

        if (data.messages.length === 0) {
            container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center p-6 text-text-muted">
                    <span class="material-symbols-outlined text-3xl mb-1 opacity-50">waving_hand</span>
                    <p class="text-xs">Aucun message pour l'instant. Dites bonjour !</p>
                </div>
            `;
        } else {
            data.messages.forEach(msg => {
                appendMessage(msg);
                lastMessageId = Math.max(lastMessageId, msg.id);
            });
            scrollToBottom();
        }
    });
}

function pollNewMessages(convId) {
    if (!lastMessageId) return;
    const url = `/${routePrefix}/messages/${convId}/messages?after_id=${lastMessageId}`;

    fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(msg => {
                appendMessage(msg);
                lastMessageId = Math.max(lastMessageId, msg.id);
            });
            scrollToBottom();
            refreshConversationsList();
        }
    });
}

function refreshMessages() {
    if (currentConversationId) loadMessages(currentConversationId);
}

function appendMessage(msg) {
    const container = document.getElementById('messagesBody');
    const isMe = msg.is_me;

    const div = document.createElement('div');
    div.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} my-1`;

    let mediaHtml = '';
    if (msg.type === 'image' && msg.file_url) {
        mediaHtml = `
            <div class="mb-1.5 cursor-pointer rounded-lg overflow-hidden max-w-[260px] border border-black/5" onclick="openLightbox('${msg.file_url}')">
                <img src="${msg.file_url}" alt="Photo" class="w-full h-auto object-cover max-h-60 hover:scale-102 transition-transform">
            </div>
        `;
    } else if (msg.type === 'video' && msg.file_url) {
        mediaHtml = `
            <div class="mb-1.5 rounded-lg overflow-hidden max-w-[280px] bg-black">
                <video src="${msg.file_url}" controls class="w-full max-h-60 rounded-lg"></video>
            </div>
        `;
    } else if (msg.type === 'audio' && msg.file_url) {
        mediaHtml = `
            <div class="mb-1.5 flex items-center gap-2 p-1 rounded-lg">
                <audio src="${msg.file_url}" controls class="h-8 max-w-[220px]"></audio>
            </div>
        `;
    } else if (msg.type === 'file' && msg.file_url) {
        mediaHtml = `
            <a href="${msg.file_url}" target="_blank" download class="mb-1.5 flex items-center gap-2 p-2 rounded-lg bg-black/5 hover:bg-black/10 transition-colors text-xs font-medium truncate max-w-[240px]">
                <span class="material-symbols-outlined text-primary text-lg">description</span>
                <span class="truncate flex-1">${msg.file_name || 'Document'}</span>
                <span class="material-symbols-outlined text-sm">download</span>
            </a>
        `;
    }

    const textContentHtml = msg.content ? `<p class="text-xs leading-relaxed whitespace-pre-wrap">${escapeHtml(msg.content)}</p>` : '';

    const bubbleBg = isMe 
        ? 'bg-primary text-white rounded-2xl rounded-tr-xs shadow-xs' 
        : 'bg-white text-on-surface rounded-2xl rounded-tl-xs shadow-xs border border-outline-variant/30';

    const senderHeader = !isMe ? `<span class="text-[10px] font-bold text-primary block mb-0.5">${escapeHtml(msg.sender_name)} <span class="text-[9px] font-normal text-text-muted">(${escapeHtml(msg.sender_role)})</span></span>` : '';

    div.innerHTML = `
        <div class="max-w-[80%] md:max-w-[70%] p-2.5 ${bubbleBg}">
            ${senderHeader}
            ${mediaHtml}
            ${textContentHtml}
            <span class="text-[9px] block text-right mt-1 ${isMe ? 'text-white/70' : 'text-text-muted'}">${msg.created_at}</span>
        </div>
    `;

    container.appendChild(div);
}

function sendMessage() {
    if (!currentConversationId) return;

    const textInput = document.getElementById('messageTextInput');
    const content = textInput.value.trim();

    if (!content && !selectedFile) return;

    const formData = new FormData();
    if (content) formData.append('content', content);
    if (selectedFile) formData.append('file', selectedFile);

    const baseUrl = `/${routePrefix}/messages/${currentConversationId}/send`;

    textInput.value = '';
    textInput.style.height = 'auto';
    clearSelectedFile();

    fetch(baseUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            appendMessage(res.message);
            lastMessageId = Math.max(lastMessageId, res.message.id);
            scrollToBottom();
            refreshConversationsList();
        }
    });
}

function refreshConversationsList() {
    fetch(`/${routePrefix}/messages/conversations/json`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.unread_total !== undefined) {
            document.getElementById('unreadTotalBadge').textContent = data.unread_total;
        }
    });
}

function scrollToBottom() {
    const el = document.getElementById('messagesBody');
    el.scrollTop = el.scrollHeight;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

// Filtres de recherche
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('conversationSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.conversation-item').forEach(item => {
                const title = item.dataset.title || '';
                item.style.display = (!q || title.includes(q)) ? '' : 'none';
            });
        });
    }

    const contactFilter = document.getElementById('contactFilterInput');
    if (contactFilter) {
        contactFilter.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.contact-card').forEach(card => {
                const name = card.dataset.name || '';
                const role = card.dataset.role || '';
                card.style.display = (!q || name.includes(q) || role.includes(q)) ? '' : 'none';
            });
        });
    }
});
</script>
@endpush
