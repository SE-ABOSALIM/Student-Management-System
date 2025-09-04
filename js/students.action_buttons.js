const searchInput = document.getElementById('searchInput');
const tableBody = document.getElementById('studentTableBody');
const editModal = document.getElementById('editModal');
const coursesModal = document.getElementById('coursesModal');
const deleteModal = document.getElementById('deleteConfirmModal');

function confirmDelete(studentId, studentName) {
    // Modal içeriğini ayarla
    document.getElementById('deleteStudentId').value = studentId;
    document.getElementById('studentNameSpan').textContent = studentName;
    
    // Modalı göster
    deleteModal.style.display = 'block';
}

function closeDeleteModal() {
    deleteModal.style.display = 'none';
}

// Modal dışına tıklandığında kapat
window.onclick = function(event) {
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}

// ESC tuşu ile kapatma
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeDeleteModal();
    }
});


function openEditModal(student) {
    document.getElementById('edit_id').value = student.id;
    document.getElementById('student_id').value = student.student_id;
    document.getElementById('name').value = student.name;
    document.getElementById('surname').value = student.surname;
    document.getElementById('phone_number').value = student.phone_number;
    document.getElementById('email').value = student.email;
    document.getElementById('attendance').value = student.attendance;
    editModal.style.display = 'block';
}

function closeEditModal() {
    editModal.style.display = 'none';
}

async function openCoursesModal(studentId) {
    try {
        const response = await fetch(`./database/get_student_courses.php?id=${studentId}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const courses = await response.json();
        const coursesList = document.getElementById('coursesList');
        
        if(courses.length > 0) {
            coursesList.innerHTML = courses.map(course => `
                <div class="course-item">
                    <h3>${course.course_name}</h3>
                    <div class="instructor">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Instructor: ${course.instructor_name}</p>
                    </div>
                </div>
            `).join('');
        } else {
            coursesList.innerHTML = '<div class="course-item"><p>No courses assigned to this student.</p></div>';
        }
        
        coursesModal.style.display = 'block';
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading courses. Please try again.');
    }
}

function closeCoursesModal() {
    coursesModal.style.display = 'none';
}

// Search functionality
searchInput.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = tableBody.getElementsByTagName('tr');
    
    Array.from(rows).forEach(row => {
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let cell of cells) {
            if (cell.textContent.toLowerCase().includes(searchValue)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    });
});

window.addEventListener('load', checkScreenSize);
window.addEventListener('resize', checkScreenSize);

setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.display = 'none';
    });
}, 3000);