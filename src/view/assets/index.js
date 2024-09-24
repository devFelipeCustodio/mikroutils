(async () => {
    document.getElementById('search_query').focus();
    const table = document.querySelector('table');

    function clipboardNotification(item) {
        M.toast({ html: `${item} foi copiado!` });
    }

    if (table)
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
                await clipboard.writeText(
                    e.target.textContent.replace(/ *\([^)]*\) */g, '')
                );
                clipboardNotification('MAC do cliente');
            }
        });

    document.addEventListener('DOMContentLoaded', function () {
        const modalElems = document.querySelectorAll('.modal');
        M.Modal.init(modalElems);
        const selectElems = document.querySelectorAll('select');
        M.FormSelect.init(selectElems, {});
    });

    const queryForm = document.querySelector('form');
    queryForm.addEventListener('submit', (e) => {
        e.preventDefault();
        e.originalTarget[0].value = e.originalTarget[0].value.trim();
        queryForm.submit();
    });
})();
