@push('styles')
<style>
    .user-form-panel {
        max-width: 780px;
        padding: 28px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: grid;
        gap: 7px;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-weight: 700;
    }

    .form-group input {
        width: 100%;
        box-sizing: border-box;
        padding: 13px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font: inherit;
    }

    .password-field {
        position: relative;
    }

    .password-field input {
        padding-right: 52px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        cursor: pointer;
        font-size: 20px;
    }

    .field-error {
        color: #b42318;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 25px;
    }

    @media (max-width: 650px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group-full {
            grid-column: auto;
        }
    }
</style>
@endpush