@push('scripts')
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(
                button.dataset.passwordToggle
            );

            const visible = input.type === 'text';

            input.type = visible ? 'password' : 'text';
            button.textContent = visible ? '👁' : '🙈';
        });
    });
</script>
@endpush