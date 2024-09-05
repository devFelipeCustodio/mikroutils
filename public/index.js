(async () => {
    const table = document.querySelector('table');

    function clipboardNotification(item) {
        M.toast({ text: `${item} foi copiado!` });
    }

    table.addEventListener('click', async (e) => {
        if (e.target.classList.contains('gw-name')) {
            const ip = e.target.parentElement.getAttribute('data-gw-ip');
            await clipboard.writeText(ip);
            clipboardNotification('IP do GW');
        } else if (e.target.classList.contains('name')) {
            await clipboard.writeText(e.target.innerText);
            clipboardNotification('Nome do usuário');
        } else if (e.target.classList.contains('address')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('IP remoto do cliente');
        } else if (e.target.classList.contains('caller-id')) {
            await clipboard.writeText(e.target.textContent);
            clipboardNotification('MAC do cliente');
        }
    });
})();
