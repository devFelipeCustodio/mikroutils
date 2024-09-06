(async () => {
    const container = document.querySelector('.data-container');
    const btnBack = document.querySelector('.btn-back');

    function clipboardNotification(item) {
        M.toast({ html: `${item} foi copiado!` });
    }
    if (container)
        container.addEventListener('click', async (e) => {
            console.log(e.target);
            if (e.target.classList.contains('user')) {
                await clipboard.writeText(e.target.textContent);
                clipboardNotification('Nome do usuário');
            } else if (e.target.classList.contains('local-address')) {
                await clipboard.writeText(e.target.textContent);
                clipboardNotification('IP local');
            } else if (e.target.classList.contains('caller-id')) {
                await clipboard.writeText(e.target.textContent);
                clipboardNotification('MAC do cliente');
            } else if (e.target.classList.contains('remote-address')) {
                await clipboard.writeText(e.target.textContent);
                clipboardNotification('IP remoto');
            } else if (e.target.classList.contains('interface')) {
                await clipboard.writeText(e.target.textContent);
                clipboardNotification('A interface');
            } else if (e.target.classList.contains('gateway')) {
                await clipboard.writeText(e.target.getAttribute('data-gw-ip'));
                clipboardNotification('IP do gateway');
            }
        });
    if (btnBack) btnBack.addEventListener('click', () => history.back());
})();
