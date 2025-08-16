<script>

    function handleBlurSub(event) {
        const div = event.currentTarget;
        const id = div.dataset.id;
        const newName = div.textContent.trim();

        fetch('/barbershopSupplies/admin/includes/sub-category-update-name.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, name: newName })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                console.error('Error updating sub-category:', data.message);
            } else {
                console.log(data.message); 
            }
        });
    }
    function handleParentSub(event) {
        if (event.target.classList.contains('parent-category')) {
            const subCategoryId = event.target.dataset.id;
            const newMainCategoryId = event.target.value;

            fetch('/barbershopSupplies/admin/includes/update-parent-sub.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `sub_category_id=${encodeURIComponent(subCategoryId)}&main_category_id=${encodeURIComponent(newMainCategoryId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(data.message); 
                } else {
                    console.error('Error updating parent category:', data.error);
                }
            })
            .catch(err => console.error('Fetch error:', err));
        }
    }
    // function handleDeleteSub(event) {
    //     const deleteBtn = event.currentTarget;
    //     const subCategoryId = deleteBtn.dataset.id;

    //     fetch('/barbershopSupplies/admin/includes/delete-subcategory.php', {
    //         method: 'POST',
    //         headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    //         body: `sub_category_id=${encodeURIComponent(subCategoryId)}`
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         if (data.success) {
    //             console.log('Subcategory deleted successfully.');
    //             deleteBtn.closest('.subcategory-row').remove();
    //         } else {
    //             console.error('Error deleting subcategory:', data.error);
    //         }
    //     })
    //     .catch(err => console.error('Fetch error:', err));
    // }
    function handleDeleteSub(event) {
        const deleteBtn = event.currentTarget;
        const subCategoryId = deleteBtn.dataset.id;

        showConfirmModal(
            "Delete sub category?",
            () => { 
                fetch('/barbershopSupplies/admin/includes/delete-subcategory.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `sub_category_id=${encodeURIComponent(subCategoryId)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        deleteBtn.closest('.subcategory-row').remove();
                    } else if (data.error === 'HasProducts') {
                        showAlertModal("Cannot delete this subcategory because it has products assigned.", () => {});
                    } else {
                        showAlertModal("Error deleting subcategory: " + data.error, () => {});
                    }
                })
                .catch(err => showAlertModal("Fetch error: " + err, () => {}));
            },
            () => {
            }
        );
    }

    function addEventSubName(){
        document.querySelectorAll('.product-grid .name[contenteditable="true"]').forEach(div => {
            div.addEventListener('blur', handleBlurSub);
        });
        document.querySelectorAll('.parent-category').forEach(select => {
            select.addEventListener('change', handleParentSub);
        });
        document.querySelectorAll('.delete-icon').forEach(btn => {
            btn.addEventListener('click', handleDeleteSub);
        });
    }


</script>
