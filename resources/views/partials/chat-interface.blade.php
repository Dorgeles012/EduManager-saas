@props([
    'routePrefix' => 'personnel',
    'conversations' => collect(),
    'groups' => [],
    'contacts' => [],
    'unreadTotal' => 0
])

<div class="chat-container glass-card rounded-2xl overflow-hidden border border-outline-variant/40 shadow-xl flex bg-white select-none flex-1 min-h-[380px] max-h-[620px] md:h-[calc(100vh-210px)] h-[calc(100vh-180px)]">
    <!-- Colonne gauche : Liste des conversations -->
    <div class="chat-sidebar w-full md:w-[320px] lg:w-[360px] border-r border-outline-variant/30 flex flex-col flex-shrink-0 bg-[#FAFAFA] h-full overflow-hidden transition-all" id="chatSidebar">
        <!-- Header gauche -->
        <div class="p-3.5 border-b border-surface-subtle bg-white/80 backdrop-blur-sm flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-xl">forum</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-sm font-bold text-on-surface">Discussions</h3>
                    <p class="text-[11px] text-text-muted">
                        <span id="unreadTotalBadge" class="font-semibold text-primary">{{ $unreadTotal }}</span> non lu(s)
                    </p>
                </div>
            </div>
            <button type="button" onclick="openNewChatModal()" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white hover:opacity-90 active:scale-95 transition-all shadow-sm" title="Nouvelle discussion">
                <span class="material-symbols-outlined text-lg">add</span>
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="p-2.5 border-b border-surface-subtle bg-white flex-shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-base">search</span>
                <input type="text" id="conversationSearchInput" placeholder="Rechercher une discussion..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-outline-variant/60 rounded-xl bg-surface-container-low/30 focus:bg-white focus:ring-primary focus:border-primary transition-all">
            </div>
        </div>

        <!-- Liste des discussions -->
        <div class="flex-1 overflow-y-auto custom-scrollbar divide-y divide-surface-subtle/50 min-h-0 bg-white" id="conversationsList">
            @forelse($conversations as $conv)
                <div class="conversation-item p-3 flex items-start gap-3 cursor-pointer hover:bg-surface-container-low/70 transition-colors relative border-l-4 border-transparent"
                     data-id="{{ $conv['id'] }}"
                     data-title="{{ strtolower($conv['title']) }}"
                     onclick="selectConversation(this.dataset.id)">
                    
                    <!-- Avatar / Icône groupe -->
                    <div class="relative flex-shrink-0">
                        @if($conv['is_class_group'] ?? false)
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-xl">groups</span>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-xs font-bold overflow-hidden border border-outline-variant/30">
                                @if($conv['avatar'] ?? false)
                                    <img src="{{ $conv['avatar'] }}" alt="" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($conv['title'], 0, 2)) }}
                                @endif
                            </div>
                        @endif

                        <span class="conversation-unread-badge absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white {{ ($conv['unread_count'] ?? 0) > 0 ? '' : 'hidden' }}">
                            {{ $conv['unread_count'] ?? 0 }}
                        </span>
                    </div>

                    <!-- Infos discussion -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <h4 class="text-xs font-semibold text-on-surface truncate conversation-title-el">{{ $conv['title'] }}</h4>
                            <span class="text-[10px] text-text-muted whitespace-nowrap flex-shrink-0 conversation-time-el">{{ $conv['last_message_time'] ?? '' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <p class="text-[11px] text-text-muted truncate flex-1 conversation-snippet-el">{{ $conv['last_message'] ?? ($conv['subtitle'] ?? '') }}</p>
                            @if($conv['is_class_group'] ?? false)
                                <span class="px-1.5 py-0.2 bg-secondary-container/60 text-secondary text-[9px] font-bold rounded uppercase flex-shrink-0">Groupe</span>
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
    <div class="chat-main flex-1 flex flex-col bg-[#F8FAFC] h-full overflow-hidden relative" id="chatMain">
        <!-- État vide (aucune discussion sélectionnée) -->
        <div id="noChatSelected" class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-surface-container-lowest h-full">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/10 to-primary-container/20 text-primary flex items-center justify-center mb-3 shadow-xs">
                <span class="material-symbols-outlined text-3xl">chat_bubble</span>
            </div>
            <h3 class="text-sm font-bold text-on-surface mb-1">Vos messages EduManager</h3>
            <p class="text-xs text-text-muted max-w-xs">Sélectionnez une discussion ou commencez un nouvel échange avec vos contacts autorisés.</p>
            <button type="button" onclick="openNewChatModal()" class="mt-3.5 px-3.5 py-1.5 bg-primary text-white rounded-lg text-xs font-medium hover:opacity-90 transition-all shadow-sm inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">add_comment</span>
                Nouvelle conversation
            </button>
        </div>

        <!-- Chat actif (masqué tant qu'aucune conversation n'est chargée) -->
        <div id="activeChat" class="hidden flex-1 flex flex-col h-full overflow-hidden">
            <!-- Entête du chat -->
            <div class="chat-header px-4 py-2.5 border-b border-outline-variant/30 bg-white flex items-center justify-between shadow-xs flex-shrink-0 z-10">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="backToSidebar()" class="md:hidden inline-flex items-center justify-center w-8 h-8 text-text-muted hover:text-on-surface rounded-lg hover:bg-surface-container">
                        <span class="material-symbols-outlined text-xl">arrow_back</span>
                    </button>
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs overflow-hidden border border-outline-variant/30" id="activeChatAvatar">
                        <span class="material-symbols-outlined text-lg">person</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-on-surface leading-tight truncate max-w-[220px] md:max-w-[340px]" id="activeChatTitle">Discussion</h4>
                        <p class="text-[10px] text-text-muted leading-tight" id="activeChatSubtitle">Enseignant</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <!-- Indicateur de connexion polling -->
                    <span id="pollingIndicator" class="w-2 h-2 rounded-full bg-success-green inline-block" title="Messagerie en temps réel active"></span>
                    <button type="button" onclick="refreshMessages()" class="w-8 h-8 rounded-lg text-text-muted hover:bg-surface-container flex items-center justify-center transition-colors" title="Actualiser la conversation">
                        <span class="material-symbols-outlined text-base">refresh</span>
                    </button>
                </div>
            </div>

            <!-- Zone de défilement des messages (SEULE ZONE QUI SCROLLE) -->
            <div class="messages-body flex-1 overflow-y-auto p-4 space-y-2.5 custom-scrollbar bg-[#F8FAFC] min-h-0" id="messagesBody">
                <!-- Les bulles de messages seront injectées dynamiquement ici -->
            </div>

            <!-- Barre d'enregistrement vocal active -->
            <div id="voiceRecordingBar" class="hidden px-4 py-2.5 bg-red-50 border-t border-red-200 flex items-center justify-between flex-shrink-0 z-10">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-ping"></span>
                    <span class="text-xs font-bold text-red-700">Enregistrement audio en cours...</span>
                    <span class="font-mono text-xs text-red-900 font-bold ml-1.5" id="recordingTimer">00:00</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="cancelVoiceRecording()" class="px-2.5 py-1 text-xs text-red-700 hover:bg-red-100 rounded-lg font-medium">
                        Annuler
                    </button>
                    <button type="button" onclick="stopAndSendVoiceRecording()" class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 flex items-center gap-1 shadow-xs">
                        <span class="material-symbols-outlined text-sm">send</span>
                        Envoyer
                    </button>
                </div>
            </div>

            <!-- Aperçu de fichier sélectionné -->
            <div id="filePreviewBar" class="hidden px-3.5 py-1.5 bg-surface-container-low border-t border-outline-variant/30 flex items-center justify-between flex-shrink-0 z-10">
                <div class="flex items-center gap-2 text-xs text-on-surface truncate">
                    <span class="material-symbols-outlined text-primary text-base" id="filePreviewIcon">attach_file</span>
                    <span class="truncate font-medium text-[11px]" id="filePreviewName">fichier.pdf</span>
                </div>
                <button type="button" onclick="clearSelectedFile()" class="text-text-muted hover:text-alert-red">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
            </div>

            <!-- Barre de saisie inférieure (FIXE EN BAS DU CHAT) -->
            <div class="chat-footer p-2.5 bg-white border-t border-outline-variant/30 flex items-end gap-2 flex-shrink-0 z-10">
                <!-- Bouton pièce jointe -->
                <label class="inline-flex items-center justify-center w-8 h-8 rounded-full text-text-muted hover:text-primary hover:bg-surface-container cursor-pointer transition-colors flex-shrink-0" title="Ajouter une photo, vidéo ou document">
                    <span class="material-symbols-outlined text-xl">attach_file</span>
                    <input type="file" id="chatFileInput" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt" onchange="handleFileSelected(this)">
                </label>

                <!-- Bouton note vocale -->
                <button type="button" onclick="startVoiceRecording()" class="inline-flex items-center justify-center w-8 h-8 rounded-full text-text-muted hover:text-primary hover:bg-surface-container transition-colors flex-shrink-0" title="Enregistrer une note vocale">
                    <span class="material-symbols-outlined text-xl">mic</span>
                </button>

                <!-- Zone de texte extensible -->
                <div class="flex-1 min-h-[36px] max-h-28 bg-surface-container-low/40 rounded-xl border border-outline-variant/40 focus-within:border-primary focus-within:bg-white transition-all px-3 py-1.5 flex items-center">
                    <textarea id="messageTextInput" rows="1" placeholder="Écrivez un message..." class="w-full bg-transparent border-0 outline-none text-xs text-on-surface resize-none max-h-24 placeholder:text-text-muted" onkeydown="handleKeypress(event)" oninput="autoResizeTextarea(this)"></textarea>
                </div>

                <!-- Bouton envoyer -->
                <button type="button" onclick="sendMessage()" id="sendMessageBtn" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white hover:opacity-90 active:scale-95 transition-all shadow-sm flex-shrink-0" title="Envoyer">
                    <span class="material-symbols-outlined text-base">send</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Conversation -->
<div class="fixed inset-0 z-[200] hidden items-center justify-center p-4" id="newChatModal">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="closeNewChatModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all max-h-[85vh] flex flex-col">
        <div class="px-4 py-3.5 border-b border-surface-subtle bg-surface-container-low/40 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">chat_add_on</span>
                <h3 class="font-headline-sm text-sm font-bold text-on-surface">Nouvelle discussion</h3>
            </div>
            <button type="button" onclick="closeNewChatModal()" class="text-text-muted hover:text-on-surface">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>

        <div class="p-3 border-b border-surface-subtle flex-shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-muted text-base">search</span>
                <input type="text" id="contactFilterInput" placeholder="Rechercher un contact..." class="w-full pl-8 pr-3 py-1.5 text-xs border border-outline-variant rounded-lg bg-surface-container-low/20 focus:bg-white focus:ring-primary focus:border-primary">
            </div>
        </div>

        <div class="p-4 overflow-y-auto custom-scrollbar flex-1 space-y-4 min-h-0">
            <!-- Groupes autorisés -->
            @if(!empty($groups))
            <div>
                <h4 class="text-[11px] font-bold uppercase tracking-wider text-text-muted mb-2">Groupes de classe</h4>
                <div class="space-y-1">
                    @foreach($groups as $grp)
                        <div class="p-2.5 rounded-xl hover:bg-surface-container cursor-pointer flex items-center gap-3 transition-colors border border-outline-variant/30"
                             data-conversation-id="{{ $grp['conversation_id'] }}"
                             onclick="selectConversation(this.dataset.conversationId); closeNewChatModal();">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center shadow-xs">
                                <span class="material-symbols-outlined text-lg">groups</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-semibold text-on-surface truncate">{{ $grp['name'] }}</h5>
                                <p class="text-[10px] text-text-muted">{{ $grp['level'] ?? 'Groupe de classe' }}</p>
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
                                 data-user-id="{{ $cnt['user_id'] }}"
                                 onclick="startDirectChat(this.dataset.userId)">
                                <div class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-xs font-bold overflow-hidden">
                                    @if($cnt['avatar'] ?? false)
                                        <img src="{{ $cnt['avatar'] }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($cnt['name'], 0, 2)) }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-semibold text-on-surface truncate">{{ $cnt['name'] }}</h5>
                                    <p class="text-[10px] text-text-muted">{{ $cnt['role'] }} • {{ $cnt['email'] }}</p>
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

<!-- Modal Visualisation Image Lightbox -->
<div class="fixed inset-0 z-[300] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-md" id="imageLightboxModal" onclick="this.classList.add('hidden'); this.classList.remove('flex');">
    <div class="relative max-w-4xl max-h-[90vh] overflow-hidden rounded-xl">
        <img id="lightboxImage" src="" alt="Aperçu image" class="max-w-full max-h-[85vh] object-contain rounded-lg">
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const routePrefix = "{{ $routePrefix }}";
    const csrfToken = "{{ csrf_token() }}";

    // ─── État global ──────────────────────────────────────────────────────────
    let currentConversationId = null;
    let lastMessageId = 0;
    let selectedFile = null;
    let globalPollingTimer = null;
    let messagePollingTimer = null;
    let knownConversationIds = new Set();

    // Variables pour l'enregistrement audio
    let mediaRecorder = null;
    let audioChunks = [];
    let recordingInterval = null;
    let recordingSeconds = 0;

    // Indexer les conversations déjà connues depuis le rendu serveur
    document.querySelectorAll('.conversation-item').forEach(el => {
        knownConversationIds.add(parseInt(el.dataset.id));
    });

    // ─── Modales ──────────────────────────────────────────────────────────────
    window.openNewChatModal = function() {
        const m = document.getElementById('newChatModal');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
    };

    window.closeNewChatModal = function() {
        const m = document.getElementById('newChatModal');
        if (m) { m.classList.remove('flex'); m.classList.add('hidden'); }
    };

    window.backToSidebar = function() {
        const sidebar = document.getElementById('chatSidebar');
        const main = document.getElementById('chatMain');
        if (sidebar && main) {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('w-full');
            main.classList.add('hidden');
        }
    };

    window.autoResizeTextarea = function(textarea) {
        if (!textarea) return;
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 96) + 'px';
    };

    window.handleKeypress = function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            window.sendMessage();
        }
    };

    window.openLightbox = function(url) {
        const modal = document.getElementById('imageLightboxModal');
        const img = document.getElementById('lightboxImage');
        if (modal && img) {
            img.src = url;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    };

    window.handleFileSelected = function(input) {
        if (input && input.files && input.files[0]) {
            selectedFile = input.files[0];
            const previewName = document.getElementById('filePreviewName');
            const previewIcon = document.getElementById('filePreviewIcon');
            const previewBar = document.getElementById('filePreviewBar');
            if (previewName) previewName.textContent = selectedFile.name;
            let icon = 'attach_file';
            if (selectedFile.type.startsWith('image/')) icon = 'image';
            else if (selectedFile.type.startsWith('video/')) icon = 'movie';
            else if (selectedFile.type.startsWith('audio/')) icon = 'audiotrack';
            else if (selectedFile.type.includes('pdf')) icon = 'picture_as_pdf';
            if (previewIcon) previewIcon.textContent = icon;
            if (previewBar) previewBar.classList.remove('hidden');
        }
    };

    window.clearSelectedFile = function() {
        selectedFile = null;
        const fileInput = document.getElementById('chatFileInput');
        const previewBar = document.getElementById('filePreviewBar');
        if (fileInput) fileInput.value = '';
        if (previewBar) previewBar.classList.add('hidden');
    };

    // ─── Enregistrement vocal ─────────────────────────────────────────────────
    window.startVoiceRecording = async function() {
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert("Votre navigateur ne supporte pas l'enregistrement audio.");
                return;
            }
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            let options = {};
            if (window.MediaRecorder && typeof MediaRecorder.isTypeSupported === 'function') {
                if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
                    options = { mimeType: 'audio/webm;codecs=opus' };
                } else if (MediaRecorder.isTypeSupported('audio/webm')) {
                    options = { mimeType: 'audio/webm' };
                } else if (MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')) {
                    options = { mimeType: 'audio/ogg;codecs=opus' };
                } else if (MediaRecorder.isTypeSupported('audio/ogg')) {
                    options = { mimeType: 'audio/ogg' };
                } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
                    options = { mimeType: 'audio/mp4' };
                }
            }
            try {
                mediaRecorder = options.mimeType ? new MediaRecorder(stream, options) : new MediaRecorder(stream);
            } catch (e) {
                mediaRecorder = new MediaRecorder(stream);
            }
            audioChunks = [];
            mediaRecorder.ondataavailable = event => {
                if (event.data && event.data.size > 0) audioChunks.push(event.data);
            };
            mediaRecorder.start(250);
            recordingSeconds = 0;
            const timer = document.getElementById('recordingTimer');
            const bar = document.getElementById('voiceRecordingBar');
            if (timer) timer.textContent = '00:00';
            if (bar) bar.classList.remove('hidden');
            recordingInterval = setInterval(() => {
                recordingSeconds++;
                const mins = String(Math.floor(recordingSeconds / 60)).padStart(2, '0');
                const secs = String(recordingSeconds % 60).padStart(2, '0');
                const timerEl = document.getElementById('recordingTimer');
                if (timerEl) timerEl.textContent = `${mins}:${secs}`;
            }, 1000);
        } catch (err) {
            console.error("Erreur accès micro:", err);
            alert("Impossible d'accéder au microphone. Veuillez autoriser l'accès dans les paramètres de votre navigateur.");
        }
    };

    window.cancelVoiceRecording = function() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            if (mediaRecorder.stream) mediaRecorder.stream.getTracks().forEach(t => t.stop());
        }
        if (recordingInterval) clearInterval(recordingInterval);
        const bar = document.getElementById('voiceRecordingBar');
        if (bar) bar.classList.add('hidden');
        audioChunks = [];
    };

    window.stopAndSendVoiceRecording = function() {
        if (!mediaRecorder || mediaRecorder.state === 'inactive') return;
        const duration = Math.max(1, recordingSeconds);
        const rawMime = mediaRecorder.mimeType || 'audio/webm';
        const recordedMime = rawMime.split(';')[0];
        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: recordedMime });
            let ext = 'webm';
            if (recordedMime.includes('ogg')) ext = 'ogg';
            else if (recordedMime.includes('mp4') || recordedMime.includes('aac') || recordedMime.includes('m4a')) ext = 'm4a';
            else if (recordedMime.includes('wav')) ext = 'wav';
            else if (recordedMime.includes('mpeg') || recordedMime.includes('mp3')) ext = 'mp3';
            const audioFile = new File([audioBlob], `vocal_${Date.now()}.${ext}`, { type: recordedMime });
            window.sendMediaDirectly(audioFile, 'audio', duration);
            if (mediaRecorder.stream) mediaRecorder.stream.getTracks().forEach(t => t.stop());
        };
        mediaRecorder.stop();
        if (recordingInterval) clearInterval(recordingInterval);
        const bar = document.getElementById('voiceRecordingBar');
        if (bar) bar.classList.add('hidden');
    };

    window.sendMediaDirectly = function(file, type, duration = null) {
        if (!currentConversationId) return;
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        if (duration) formData.append('duration', duration);
        fetch(`/${routePrefix}/messages/${currentConversationId}/send`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.message) {
                window.appendMessage(res.message);
                lastMessageId = Math.max(lastMessageId, res.message.id);
                window.scrollToBottom(true);
            }
        })
        .catch(err => console.error("Erreur envoi média:", err));
    };

    // ─── Démarrer une conversation directe ───────────────────────────────────
    window.startDirectChat = function(userId) {
        window.closeNewChatModal();
        fetch(`/${routePrefix}/messages/start`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ recipient_id: parseInt(userId) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.conversation_id) {
                // Rafraîchir la liste puis sélectionner
                window.refreshConversationsList(function() {
                    window.selectConversation(data.conversation_id);
                });
            } else if (data.message) {
                alert(data.message);
            }
        })
        .catch(err => console.error("Erreur démarrage chat:", err));
    };

    // ─── Sélection d'une conversation ────────────────────────────────────────
    window.selectConversation = function(convId) {
        if (!convId) return;
        const newId = parseInt(convId);
        currentConversationId = newId;
        lastMessageId = 0;

        // Sur mobile : basculer l'affichage vers le chat
        if (window.innerWidth < 768) {
            const sidebar = document.getElementById('chatSidebar');
            const main = document.getElementById('chatMain');
            if (sidebar) sidebar.classList.add('hidden');
            if (main) main.classList.remove('hidden');
        }

        const noChat = document.getElementById('noChatSelected');
        const activeChat = document.getElementById('activeChat');
        if (noChat) noChat.classList.add('hidden');
        if (activeChat) activeChat.classList.remove('hidden');

        // Mettre en surbrillance l'item actif dans la liste
        document.querySelectorAll('.conversation-item').forEach(el => {
            const id = parseInt(el.dataset.id);
            if (id === newId) {
                el.classList.add('bg-primary-fixed/25', 'border-primary');
                el.classList.remove('border-transparent');
                const badge = el.querySelector('.conversation-unread-badge');
                if (badge) badge.classList.add('hidden');
            } else {
                el.classList.remove('bg-primary-fixed/25', 'border-primary');
                el.classList.add('border-transparent');
            }
        });

        window.loadMessages(newId);
    };

    // ─── Chargement complet des messages ─────────────────────────────────────
    window.loadMessages = function(convId) {
        fetch(`/${routePrefix}/messages/${convId}/messages`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            const conv = data.conversation;
            const titleEl = document.getElementById('activeChatTitle');
            const subtitleEl = document.getElementById('activeChatSubtitle');
            const avatarEl = document.getElementById('activeChatAvatar');
            if (titleEl) titleEl.textContent = conv.title;
            if (subtitleEl) subtitleEl.textContent = conv.subtitle;
            if (avatarEl) {
                if (conv.is_class_group) {
                    avatarEl.innerHTML = `<span class="material-symbols-outlined text-primary text-lg">groups</span>`;
                } else {
                    avatarEl.innerHTML = `<span class="material-symbols-outlined text-primary text-lg">person</span>`;
                }
            }
            const container = document.getElementById('messagesBody');
            if (!container) return;
            container.innerHTML = '';
            if (data.messages.length === 0) {
                container.innerHTML = `
                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-text-muted select-none">
                        <span class="material-symbols-outlined text-3xl mb-1.5 opacity-40">waving_hand</span>
                        <p class="text-xs font-medium">Aucun message pour l'instant. Dites bonjour !</p>
                    </div>
                `;
            } else {
                data.messages.forEach(msg => {
                    window.appendMessage(msg);
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
                window.scrollToBottom(false);
            }
        })
        .catch(err => console.error("Erreur chargement messages:", err));
    };

    // ─── POLLING DES NOUVEAUX MESSAGES (pour la conversation ouverte) ─────────
    function pollActiveConversation() {
        if (!currentConversationId) return;
        fetch(`/${routePrefix}/messages/${currentConversationId}/messages?after_id=${lastMessageId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.messages || data.messages.length === 0) return;
            let hasNew = false;
            data.messages.forEach(msg => {
                const existing = document.querySelector(`[data-msg-id="${msg.id}"]`);
                if (!existing) {
                    window.appendMessage(msg);
                    lastMessageId = Math.max(lastMessageId, msg.id);
                    hasNew = true;
                } else {
                    window.updateMessageStatus(msg.id, msg.status);
                }
            });
            if (hasNew) window.scrollToBottom(true);
        })
        .catch(() => {}); // silencieux pour ne pas polluer la console
    }

    // ─── POLLING GLOBAL DE LA LISTE (toujours actif même sans conversation ouverte) ──
    window.refreshConversationsList = function(callback) {
        fetch(`/${routePrefix}/messages/conversations/json`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            // Mettre à jour le badge global non-lus
            if (data.unread_total !== undefined) {
                const badge = document.getElementById('unreadTotalBadge');
                if (badge) badge.textContent = data.unread_total;
            }

            if (!data.conversations || !Array.isArray(data.conversations)) {
                if (callback) callback();
                return;
            }

            const list = document.getElementById('conversationsList');
            const emptyNotice = document.getElementById('emptyConversationsNotice');

            data.conversations.forEach(conv => {
                const convId = parseInt(conv.id);
                const existingItem = document.querySelector(`.conversation-item[data-id="${convId}"]`);

                if (existingItem) {
                    // Mettre à jour l'item existant
                    const snippet = existingItem.querySelector('.conversation-snippet-el');
                    const time = existingItem.querySelector('.conversation-time-el');
                    const unread = existingItem.querySelector('.conversation-unread-badge');
                    if (snippet && conv.last_message !== undefined) snippet.textContent = conv.last_message;
                    if (time && conv.last_message_time) time.textContent = conv.last_message_time;
                    if (unread) {
                        if (conv.unread_count > 0 && currentConversationId !== convId) {
                            unread.textContent = conv.unread_count;
                            unread.classList.remove('hidden');
                        } else {
                            unread.classList.add('hidden');
                        }
                    }
                } else {
                    // Nouvelle conversation inconnue : l'injecter dans la sidebar
                    knownConversationIds.add(convId);
                    if (emptyNotice) emptyNotice.classList.add('hidden');

                    const initials = conv.title ? conv.title.substring(0, 2).toUpperCase() : '?';
                    const avatarHtml = conv.is_class_group
                        ? `<div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white flex items-center justify-center shadow-xs"><span class="material-symbols-outlined text-xl">groups</span></div>`
                        : `<div class="w-10 h-10 rounded-full bg-primary-fixed text-primary flex items-center justify-center text-xs font-bold overflow-hidden border border-outline-variant/30">${conv.avatar ? `<img src="${window.escapeHtml(conv.avatar)}" alt="" class="w-full h-full object-cover">` : window.escapeHtml(initials)}</div>`;

                    const unreadBadge = conv.unread_count > 0
                        ? `<span class="conversation-unread-badge absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white">${conv.unread_count}</span>`
                        : `<span class="conversation-unread-badge absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white hidden">0</span>`;

                    const groupBadge = conv.is_class_group
                        ? `<span class="px-1.5 py-0.2 bg-secondary-container/60 text-secondary text-[9px] font-bold rounded uppercase flex-shrink-0">Groupe</span>`
                        : '';

                    const newItem = document.createElement('div');
                    newItem.className = 'conversation-item p-3 flex items-start gap-3 cursor-pointer hover:bg-surface-container-low/70 transition-colors relative border-l-4 border-transparent';
                    newItem.setAttribute('data-id', convId);
                    newItem.setAttribute('data-title', (conv.title || '').toLowerCase());
                    newItem.innerHTML = `
                        <div class="relative flex-shrink-0">${avatarHtml}${unreadBadge}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <h4 class="text-xs font-semibold text-on-surface truncate conversation-title-el">${window.escapeHtml(conv.title || '')}</h4>
                                <span class="text-[10px] text-text-muted whitespace-nowrap flex-shrink-0 conversation-time-el">${window.escapeHtml(conv.last_message_time || '')}</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <p class="text-[11px] text-text-muted truncate flex-1 conversation-snippet-el">${window.escapeHtml(conv.last_message || conv.subtitle || '')}</p>
                                ${groupBadge}
                            </div>
                        </div>
                    `;
                    newItem.addEventListener('click', function() {
                        window.selectConversation(this.dataset.id);
                    });

                    // Insérer en premier (conversation la plus récente)
                    if (list) list.insertBefore(newItem, list.firstChild);

                    // Notification visuelle de nouveau message
                    if (conv.unread_count > 0) {
                        newItem.classList.add('animate-pulse');
                        setTimeout(() => newItem.classList.remove('animate-pulse'), 3000);
                    }
                }
            });

            if (callback) callback();
        })
        .catch(() => {
            if (callback) callback();
        });
    };

    // ─── Mise à jour statut message ───────────────────────────────────────────
    window.updateMessageStatus = function(msgId, status) {
        const el = document.querySelector(`[data-msg-id="${msgId}"] .message-status-badge`);
        if (!el) return;
        if (status === 'read') {
            el.innerHTML = `<span class="material-symbols-outlined text-[13px] text-sky-400 font-bold" title="Lu">done_all</span>`;
        } else if (status === 'delivered') {
            el.innerHTML = `<span class="material-symbols-outlined text-[13px] text-white/70 font-normal" title="Distribué">done_all</span>`;
        } else {
            el.innerHTML = `<span class="material-symbols-outlined text-[13px] text-white/70 font-normal" title="Envoyé">check</span>`;
        }
    };

    window.refreshMessages = function() {
        if (currentConversationId) window.loadMessages(currentConversationId);
    };

    // ─── Rendu d'une bulle de message ────────────────────────────────────────
    window.appendMessage = function(msg) {
        const container = document.getElementById('messagesBody');
        if (!container) return;
        const existing = document.querySelector(`[data-msg-id="${msg.id}"]`);
        if (existing) return;

        // Vider le placeholder "Aucun message"
        const placeholder = container.querySelector('.h-full.flex.flex-col.items-center');
        if (placeholder) placeholder.remove();

        const isMe = msg.is_me;
        const isAudio = msg.type === 'audio'
            || (msg.file_name && msg.file_name.startsWith('vocal_'))
            || (msg.mime_type && msg.mime_type.startsWith('audio/'));

        const div = document.createElement('div');
        div.className = `flex flex-col ${isMe ? 'items-end' : 'items-start'} my-0.5`;
        div.setAttribute('data-msg-id', msg.id);

        let mediaHtml = '';
        if (msg.type === 'image' && msg.file_url) {
            mediaHtml = `
                <div class="mb-1.5 cursor-pointer rounded-xl overflow-hidden max-w-[260px] border border-black/5 shadow-xs" onclick="openLightbox('${msg.file_url}')">
                    <img src="${msg.file_url}" alt="Photo" class="w-full h-auto object-cover max-h-56 hover:scale-102 transition-transform">
                </div>
            `;
        } else if (isAudio && msg.file_url) {
            const dur = msg.formatted_duration || (msg.duration ? String(Math.floor(msg.duration / 60)).padStart(2, '0') + ':' + String(msg.duration % 60).padStart(2, '0') : '00:00');
            const playerBg = isMe ? 'bg-black/20 text-white' : 'bg-surface-container-low text-on-surface';
            const micColor = isMe ? 'text-white' : 'text-primary';
            mediaHtml = `
                <div class="voice-player-card flex flex-col gap-1.5 p-2 rounded-2xl ${playerBg} min-w-[240px] max-w-[320px] mb-1">
                    <div class="flex items-center justify-between px-1 text-[11px] font-medium">
                        <span class="flex items-center gap-1.5 ${micColor}">
                            <span class="material-symbols-outlined text-base">mic</span>
                            <span class="font-semibold text-[11px]">Note vocale</span>
                        </span>
                        <span class="font-mono text-[10px] opacity-80">${dur}</span>
                    </div>
                    <audio src="${msg.file_url}" controls preload="auto" class="w-full h-8 rounded-lg outline-none max-w-full">
                        <source src="${msg.file_url}" type="${msg.mime_type || 'audio/webm'}">
                        <source src="${msg.file_url}" type="audio/webm">
                        <source src="${msg.file_url}" type="audio/ogg">
                        <source src="${msg.file_url}" type="audio/mp4">
                        <source src="${msg.file_url}" type="audio/mpeg">
                        Votre navigateur ne supporte pas l'élément audio.
                    </audio>
                </div>
            `;
        } else if (msg.type === 'video' && msg.file_url) {
            mediaHtml = `
                <div class="mb-1.5 rounded-xl overflow-hidden max-w-[280px] bg-black shadow-xs">
                    <video src="${msg.file_url}" controls class="w-full max-h-56 rounded-xl"></video>
                </div>
            `;
        } else if (msg.type === 'file' && msg.file_url) {
            mediaHtml = `
                <a href="${msg.file_url}" target="_blank" download class="mb-1 flex items-center gap-2.5 p-2 rounded-xl ${isMe ? 'bg-white/10 hover:bg-white/20 text-white' : 'bg-surface-container-low hover:bg-surface-container text-on-surface'} transition-colors text-xs font-medium truncate max-w-[240px]">
                    <span class="material-symbols-outlined ${isMe ? 'text-white' : 'text-primary'} text-lg">description</span>
                    <span class="truncate flex-1 text-[11px]">${window.escapeHtml(msg.file_name || 'Document')}</span>
                    <span class="material-symbols-outlined text-sm opacity-80">download</span>
                </a>
            `;
        }

        const textContentHtml = msg.content ? `<p class="text-xs leading-relaxed whitespace-pre-wrap">${window.escapeHtml(msg.content)}</p>` : '';
        const bubbleBg = isMe
            ? 'bg-primary text-white rounded-2xl rounded-tr-xs shadow-xs'
            : 'bg-white text-on-surface rounded-2xl rounded-tl-xs shadow-xs border border-outline-variant/30';
        const senderHeader = !isMe ? `<span class="text-[10px] font-bold text-primary block mb-0.5">${window.escapeHtml(msg.sender_name || '')} <span class="text-[9px] font-normal text-text-muted">(${window.escapeHtml(msg.sender_role || '')})</span></span>` : '';

        let statusBadge = '';
        if (isMe) {
            if (msg.status === 'read') {
                statusBadge = `<span class="message-status-badge inline-flex items-center text-sky-400 font-bold ml-1" title="Lu"><span class="material-symbols-outlined text-[13px]">done_all</span></span>`;
            } else if (status === 'delivered') {
                statusBadge = `<span class="message-status-badge inline-flex items-center text-white/70 font-normal ml-1" title="Distribué"><span class="material-symbols-outlined text-[13px]">done_all</span></span>`;
            } else {
                statusBadge = `<span class="message-status-badge inline-flex items-center text-white/70 font-normal ml-1" title="Envoyé"><span class="material-symbols-outlined text-[13px]">check</span></span>`;
            }
        }

        const deleteButtonHtml = isMe ? `
            <button type="button" onclick="event.stopPropagation(); window.deleteMessage(${msg.id});" class="text-white/60 hover:text-red-300 transition-colors p-0.5 ml-1.5 inline-flex items-center" title="Supprimer ce message">
                <span class="material-symbols-outlined text-[13px]">delete</span>
            </button>
        ` : '';

        div.innerHTML = `
            <div class="max-w-[85%] md:max-w-[70%] p-2.5 ${bubbleBg} group relative">
                ${senderHeader}
                ${mediaHtml}
                ${textContentHtml}
                <div class="flex items-center justify-end gap-1 mt-0.5 text-[10px] ${isMe ? 'text-white/70' : 'text-text-muted'}">
                    <span>${window.escapeHtml(msg.created_at || '')}</span>
                    ${statusBadge}
                    ${deleteButtonHtml}
                </div>
            </div>
        `;

        container.appendChild(div);
    };

    // ─── Suppression d'un message ─────────────────────────────────────────────
    window.deleteMessage = function(msgId) {
        if (!msgId) return;

        const executeDelete = () => {
            fetch(`/${routePrefix}/messages/${msgId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const el = document.querySelector(`[data-msg-id="${msgId}"]`);
                    if (el) {
                        el.style.transition = 'all 0.3s ease';
                        el.style.opacity = '0';
                        el.style.transform = 'scale(0.85)';
                        setTimeout(() => {
                            el.remove();
                            const container = document.getElementById('messagesBody');
                            if (container && container.querySelectorAll('[data-msg-id]').length === 0) {
                                container.innerHTML = `
                                    <div class="h-full flex flex-col items-center justify-center text-center p-6 text-text-muted select-none">
                                        <span class="material-symbols-outlined text-3xl mb-1.5 opacity-40">waving_hand</span>
                                        <p class="text-xs font-medium">Aucun message pour l'instant. Dites bonjour !</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                    window.refreshConversationsList();
                } else if (res.message) {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Erreur', text: res.message });
                    } else {
                        alert(res.message);
                    }
                }
            })
            .catch(err => {
                console.error("Erreur suppression message:", err);
            });
        };

        if (window.Swal) {
            Swal.fire({
                title: 'Supprimer ce message ?',
                text: 'Le message et ses pièces jointes seront définitivement supprimés.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b'
            }).then(result => {
                if (result.isConfirmed) executeDelete();
            });
        } else {
            if (confirm('Voulez-vous vraiment supprimer définitivement ce message ?')) {
                executeDelete();
            }
        }
    };

    // ─── Envoi de message texte/fichier ──────────────────────────────────────
    window.sendMessage = function() {
        if (!currentConversationId) return;
        const textInput = document.getElementById('messageTextInput');
        if (!textInput) return;
        const content = textInput.value.trim();
        if (!content && !selectedFile) return;

        const formData = new FormData();
        if (content) formData.append('content', content);
        if (selectedFile) formData.append('file', selectedFile);

        textInput.value = '';
        textInput.style.height = 'auto';
        window.clearSelectedFile();

        fetch(`/${routePrefix}/messages/${currentConversationId}/send`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.success && res.message) {
                window.appendMessage(res.message);
                lastMessageId = Math.max(lastMessageId, res.message.id);
                window.scrollToBottom(true);
            } else if (res.message) {
                alert(res.message);
            }
        })
        .catch(err => console.error("Erreur envoi message:", err));
    };

    // ─── Scroll vers le bas ───────────────────────────────────────────────────
    window.scrollToBottom = function(smooth = false) {
        const el = document.getElementById('messagesBody');
        if (el) {
            if (smooth) {
                el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
            } else {
                el.scrollTop = el.scrollHeight;
            }
        }
    };

    // ─── Utilitaire escapeHtml ────────────────────────────────────────────────
    window.escapeHtml = function(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    // ─── Filtres de recherche ────────────────────────────────────────────────
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

    // ─── DÉMARRAGE DU POLLING GLOBAL (toujours actif dès que la page est chargée) ──
    // Cycle de polling : toutes les 2s → poll liste + poll messages si conversation ouverte
    function startGlobalPolling() {
        if (globalPollingTimer) clearInterval(globalPollingTimer);

        globalPollingTimer = setInterval(function() {
            // 1. Toujours : rafraîchir la liste des conversations et les badges non-lus
            window.refreshConversationsList();

            // 2. Si une conversation est ouverte : poll des nouveaux messages
            if (currentConversationId) {
                pollActiveConversation();
            }
        }, 2000);
    }

    // ─── AUTO-CHARGEMENT AU DÉMARRAGE ────────────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    const targetConvId = urlParams.get('conversation_id') || urlParams.get('conv');
    const firstConversationItem = document.querySelector('.conversation-item');

    if (targetConvId) {
        window.selectConversation(targetConvId);
    } else if (firstConversationItem) {
        window.selectConversation(firstConversationItem.dataset.id);
    }

    // Démarrer le polling global immédiatement
    startGlobalPolling();

    // Indicateur de santé du polling (vert = actif)
    setInterval(function() {
        const indicator = document.getElementById('pollingIndicator');
        if (!indicator) return;
        indicator.style.opacity = indicator.style.opacity === '0.3' ? '1' : '0.3';
    }, 1000);
});
</script>
@endpush