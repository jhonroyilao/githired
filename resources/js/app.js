import 'bootstrap-icons/font/bootstrap-icons.css';

document.addEventListener('change', (event) => {
    const input = event.target;

    if (!(input instanceof HTMLInputElement) || input.type !== 'file') {
        return;
    }

    const targetId = input.dataset.fileNameTarget;

    if (!targetId) {
        return;
    }

    const target = document.getElementById(targetId);

    if (!target) {
        return;
    }

    const hasFile = input.files?.length > 0;
    target.textContent = hasFile ? input.files[0].name : 'No file selected';

    const removeTargetId = input.dataset.removeTarget;
    const removeTarget = removeTargetId ? document.getElementById(removeTargetId) : null;

    if (removeTarget instanceof HTMLInputElement) {
        removeTarget.value = '0';
    }

    const uploadId = input.id;
    const removeButton = uploadId ? document.querySelector(`[data-remove-button="${uploadId}"]`) : null;
    if (removeButton instanceof HTMLElement) {
        removeButton.hidden = !hasFile;
    }

    const previewTargetId = input.dataset.previewTarget;
    const previewTarget = previewTargetId ? document.getElementById(previewTargetId) : null;
    const file = input.files?.[0] ?? null;

    if (!(previewTarget instanceof HTMLImageElement) || !file || !file.type.startsWith('image/')) {
        return;
    }

    previewTarget.src = URL.createObjectURL(file);
});

document.addEventListener('click', (event) => {
    const button = event.target instanceof Element ? event.target.closest('[data-remove-upload]') : null;

    if (!(button instanceof HTMLElement)) {
        return;
    }

    const uploadId = button.dataset.removeUpload;
    const input = uploadId ? document.getElementById(uploadId) : null;

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    input.value = '';

    const fileNameTarget = input.dataset.fileNameTarget ? document.getElementById(input.dataset.fileNameTarget) : null;

    if (fileNameTarget) {
        fileNameTarget.textContent = 'File will be cleared on save';
    }

    const removeTarget = input.dataset.removeTarget ? document.getElementById(input.dataset.removeTarget) : null;

    if (removeTarget instanceof HTMLInputElement) {
        removeTarget.value = '1';
    }

    const previewTarget = input.dataset.previewTarget ? document.getElementById(input.dataset.previewTarget) : null;

    if (previewTarget instanceof HTMLImageElement) {
        previewTarget.src = previewTarget.dataset.placeholderSrc ?? '';
    }

    button.hidden = true;
});

document.addEventListener('click', (event) => {
    const button = event.target instanceof Element ? event.target.closest('[data-password-toggle]') : null;

    if (!(button instanceof HTMLButtonElement)) {
        return;
    }

    const targetId = button.dataset.passwordToggle;
    const input = targetId ? document.getElementById(targetId) : null;

    if (!(input instanceof HTMLInputElement)) {
        return;
    }

    const shouldShow = input.type === 'password';
    input.type = shouldShow ? 'text' : 'password';

    const icon = button.querySelector('[data-password-icon]');
    if (icon instanceof HTMLElement) {
        icon.className = shouldShow ? 'bi bi-eye-slash-fill text-lg' : 'bi bi-eye-fill text-lg';
    }

    const label = shouldShow ? 'Hide password' : 'Show password';
    button.setAttribute('aria-label', label);
    button.setAttribute('title', label);
    const text = button.querySelector('[data-password-label]');
    if (text) {
        text.textContent = label;
    }
});
