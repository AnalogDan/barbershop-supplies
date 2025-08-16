<script>

    function handleNameBlur(event) {
        let el = event.currentTarget;
        let id = el.dataset.id;
        let newName = el.innerText.trim();

        fetch('/barbershopSupplies/admin/includes/update-main-category.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${encodeURIComponent(id)}&name=${encodeURIComponent(newName)}`
        })
        .then(res => res.text())
        .then(data => {
            console.log('Update response: ', data);
        })
        .catch(err => console.error('Error: ', err));
    }
    function handleDeleteMain(event) {
        const el = event.currentTarget;
        const id = el.dataset.id;

        showConfirmModal(
            "Delete main category?",
            () => {
                fetch('/barbershopSupplies/admin/includes/delete-main-category.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${encodeURIComponent(id)}`
                })
                .then(res => res.text())
                .then(data => {
                    if (data === 'Success') {
                        el.closest('.category-row').remove();
                    } else if (data === 'HasSubcategories') {
                        showAlertModal(
                            "You need to delete or move all attached subcategories before deleting.",
                            () => {}
                        );
                    } else {
                        showAlertModal("Delete failed: " + data, () => {});
                    }
                })
                .catch(err => showAlertModal("Error: " + err, () => {}));
            },
            () => {
            }
        );
    }

    function addEventMainName(){
         document.querySelectorAll('.name').forEach(el => {
            el.addEventListener('blur', handleNameBlur);
        });
    }
    function addEventMainDelete(){
        document.querySelectorAll('.delete-icon').forEach(el => {
            el.addEventListener('click', handleDeleteMain);
        });
    }



    function showConfirmModal(message, onYes, onNo) {
        const template = document.getElementById('confirmModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const yesBtn = modal.querySelector('#confirmYes');
        const noBtn = modal.querySelector('#confirmNo');
        function cleanup() {
            yesBtn.removeEventListener('click', yesHandler);
            noBtn.removeEventListener('click', noHandler);
            modal.remove();
        }
        function yesHandler() {
            cleanup();
            if (typeof onYes === 'function') onYes();
        }
        function noHandler() {
            cleanup();
            if (typeof onNo === 'function') onNo();
        }
        yesBtn.addEventListener('click', yesHandler);
        noBtn.addEventListener('click', noHandler);
    }

    function showAlertModal(message, onOk){
        const template = document.getElementById('alertModal');
        const modal = template.content.cloneNode(true).querySelector('.modal-overlay');
        document.body.appendChild(modal);
        modal.querySelector('p').textContent = message;
        modal.classList.add('show');
        const okBtn = modal.querySelector('#confirmOk');
        function cleanup() {
            okBtn.removeEventListener('click', okHandler);
            modal.remove();
        }
        function okHandler(){
            cleanup();
            if (typeof onOk === 'function'){ onOk()}
            else{};
        }
        okBtn.addEventListener('click', okHandler);
    }

</script>
