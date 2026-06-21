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

    target.textContent = input.files?.length ? input.files[0].name : 'No file selected';

    const removeTargetId = input.dataset.removeTarget;
    const removeTarget = removeTargetId ? document.getElementById(removeTargetId) : null;

    if (removeTarget instanceof HTMLInputElement) {
        removeTarget.value = '0';
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
        fileNameTarget.textContent = 'Marked for removal';
    }

    const removeTarget = input.dataset.removeTarget ? document.getElementById(input.dataset.removeTarget) : null;

    if (removeTarget instanceof HTMLInputElement) {
        removeTarget.value = '1';
    }

    const previewTarget = input.dataset.previewTarget ? document.getElementById(input.dataset.previewTarget) : null;

    if (previewTarget instanceof HTMLImageElement) {
        previewTarget.src = previewTarget.dataset.placeholderSrc ?? '';
    }
});
