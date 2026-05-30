@once
    <script>
        function showAuthAlert({ title, text = '', icon = 'info' } = {}) {
            if (window.Swal && typeof window.Swal.fire === 'function') {
                return window.Swal.fire({
                    title,
                    text,
                    icon,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3730a3',
                });
            }

            alert(text ? `${title}\n${text}` : title);
        }

        @if ($errors->any())
            showAuthAlert({
                title: 'Erreur',
                text: @json(implode("\n", $errors->all())),
                icon: 'error',
            });
        @endif

        @if (session('status'))
            showAuthAlert({
                title: 'Information',
                text: @json(session('status')),
                icon: 'success',
            });
        @endif

        @if (session('success'))
            showAuthAlert({
                title: 'Succès',
                text: @json(session('success')),
                icon: 'success',
            });
        @endif

        @if (session('error'))
            showAuthAlert({
                title: 'Erreur',
                text: @json(session('error')),
                icon: 'error',
            });
        @endif
    </script>
@endonce
