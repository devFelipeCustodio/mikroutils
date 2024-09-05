(async () => {
    const container = document.querySelector('.container');

    function clipboardNotification(item) {
        M.toast({ text: `${item} foi copiado!` });
    }

    container.addEventListener('click', async (e) => {
        console.log(e.target);
        if (e.target.classList.contains('user')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('Nome do usuário');
        } else if (e.target.classList.contains('local-address')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('IP local');
        } else if (e.target.classList.contains('remote-address')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('IP remoto');
        } else if (e.target.classList.contains('interface')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('A interface');
        }
    });
})();
