document.addEventListener("DOMContentLoaded", function () {
    const subjectSelect = document.querySelector("select[name='subject_id']");
    const teacherSelect = document.querySelector("select[name='teacher_id']");
    const classroomSelect = document.querySelector("select[name='classroom_id']");

    subjectSelect.addEventListener("change", function () {
        const subjectId = this.value;

        if (!subjectId) return;

        fetch(`get_subject_info.php?subject_id=${subjectId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    teacherSelect.value = data.teacher_id;
                    classroomSelect.value = data.classroom_id;
                }
            })
            .catch(error => console.error("Помилка при отриманні даних предмета:", error));
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.show-location').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const locationDiv = document.getElementById('location-' + id);
            
            if (!locationDiv) {
                console.warn('Location not found for ID:', id);
                return;
            }

            const isVisible = locationDiv.style.display === 'block';
            locationDiv.style.display = isVisible ? 'none' : 'block';
            this.textContent = isVisible ? 'Локація' : 'Сховати локацію';
        });
    });
});

function showSubgroups(button, items) {
    const container = button.parentElement;

    if (container.querySelector('.subgroups-container')) {
        container.querySelector('.subgroups-container').remove();
        return;
    }
    
    const subgroupsContainer = document.createElement('div');
    subgroupsContainer.className = 'subgroups-container';
    
    items.forEach(item => {
        const subgroup = document.createElement('button');
        subgroup.className = 'subgroup-button';
        subgroup.textContent = item.name;
        subgroup.onclick = function() {
            window.location.href = `userpage.php?group_id=${item.id}`;
        };
        subgroupsContainer.appendChild(subgroup);
    });
    
    container.appendChild(subgroupsContainer);
}