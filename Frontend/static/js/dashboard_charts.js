document.addEventListener("DOMContentLoaded", () => {
  fetch("/landingpage/api/analytics")
    .then(res => res.json())
    .then(data => {
      renderGradeDistribution(data.gradeDistribution);
      renderModuleAverages(data.moduleAverages);
      renderStudentSelector(data.studentTrends);
      renderGenderInsights(data.genderInsights);
      renderTopPerformers(data.topPerformers);
      renderEnrollmentTrends(data.enrollmentTrends);
      renderPassRateByModule(data.passRateByModule);
    })
    .catch(err => console.error("Chart Load Error:", err));
});

function renderGradeDistribution(distribution) {
  new Chart(document.getElementById("gradeDistribution"), {
    type: "pie",
    data: {
      labels: Object.keys(distribution),
      datasets: [{
        data: Object.values(distribution),
        backgroundColor: ["#10B981", "#3B82F6", "#F59E0B", "#EF4444"]
      }]
    }
  });
}

function renderModuleAverages(data) {
  new Chart(document.getElementById("moduleAverages"), {
    type: "bar",
    data: {
      labels: Object.keys(data),
      datasets: [{
        label: "Average Score",
        data: Object.values(data),
        backgroundColor: "#3B82F6"
      }]
    }
  });
}

function renderStudentSelector(students) {
  const select = document.getElementById("studentSelector");
  students.forEach(s => {
    const option = document.createElement("option");
    option.value = s.student_id;
    option.text = s.name;
    select.appendChild(option);
  });

  select.addEventListener("change", e => {
    const student = students.find(s => s.student_id === e.target.value);
    if (student) {
      const labels = student.modules.map(m => m.name);
      const values = student.modules.map(m => m.grade);
      new Chart(document.getElementById("studentTrend"), {
        type: "line",
        data: {
          labels: labels,
          datasets: [{
            label: "Grades",
            data: values,
            borderColor: "#10B981",
            fill: false
          }]
        }
      });
    }
  });

  select.dispatchEvent(new Event("change"));
}

function renderGenderInsights(data) {
  const labels = Object.keys(data[Object.keys(data)[0]]);
  const datasets = Object.keys(data).map((gender, idx) => ({
    label: gender,
    data: labels.map(label => data[gender][label]),
    backgroundColor: idx === 0 ? "#F59E0B" : "#6366F1"
  }));

  new Chart(document.getElementById("genderInsights"), {
    type: "bar",
    data: { labels, datasets }
  });
}

function renderTopPerformers(data) {
  new Chart(document.getElementById("topPerformers"), {
    type: "bar",
    data: {
      labels: data.map(s => s.name),
      datasets: [{
        label: "Average",
        data: data.map(s => s.average),
        backgroundColor: "#10B981"
      }]
    }
  });
}

function renderEnrollmentTrends(data) {
  new Chart(document.getElementById("enrollmentTrends"), {
    type: "line",
    data: {
      labels: Object.keys(data),
      datasets: [{
        label: "Enrollments",
        data: Object.values(data),
        borderColor: "#3B82F6",
        fill: true
      }]
    }
  });
}

function renderPassRateByModule(data) {
  const labels = Object.keys(data);
  const passData = labels.map(m => data[m].pass);
  const failData = labels.map(m => data[m].fail);

  new Chart(document.getElementById("passRateModule"), {
    type: "bar",
    data: {
      labels,
      datasets: [
        {
          label: "Pass",
          data: passData,
          backgroundColor: "#10B981"
        },
        {
          label: "Fail",
          data: failData,
          backgroundColor: "#EF4444"
        }
      ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
  });
}