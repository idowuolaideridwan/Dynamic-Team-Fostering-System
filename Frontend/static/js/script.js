async function loginUser(event) {
    event.preventDefault();  // Prevent normal form submission

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    try {
        const response = await fetch("/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, password })
        });

        const result = await response.json();

        if (response.ok && result.redirect) {
            window.location.href = result.redirect; // Redirect based on JSON response
        } else {
            document.getElementById("error-message").innerText = result.error || "Login failed";
        }

    } catch (error) {
        document.getElementById("error-message").innerText = "An error occurred while logging in.";
    }
}
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.getElementById("toggle-password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.innerText = "🙈"; // Change to hide icon
            } else {
                passwordInput.type = "password";
                toggleIcon.innerText = "👁️"; // Change to show icon
            }
        }
  function showSection(id) {
    const sections = document.querySelectorAll('main > section');
    sections.forEach(section => section.classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');
  }

  function fetchAverageResults() {
  const input = document.getElementById('studentIdsInput').value.trim();
  const query = input ? '?student_ids=' + encodeURIComponent(input) : '';
  const url = `/landingpage/api/averages${query}`;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      const tbody = document.querySelector('#averagesTable tbody');
      tbody.innerHTML = '';

      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="2" class="text-center text-gray-400 py-4">No results found</td></tr>`;
      } else {
        data.forEach(row => {
          tbody.insertAdjacentHTML('beforeend', `
            <tr class="border-t border-gray-700">
              <td class="px-4 py-2">${row.student_id}</td>
              <td class="px-4 py-2">${row.average}%</td>
            </tr>
          `);
        });
      }
    })
    .catch(err => {
      console.error("Error loading averages:", err);
    });
}

  $(document).ready(function () {
    $('#studentsTable').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: ['csvHtml5', 'excelHtml5']
    });

    $('#gradesTable').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: ['csvHtml5', 'excelHtml5']
    });

    $('#averagesTable').DataTable({
      responsive: true,
      dom: 'Bfrtip',
      buttons: ['csvHtml5', 'excelHtml5']
    });
  });
