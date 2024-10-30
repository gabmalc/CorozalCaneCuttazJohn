let grades = [91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 89];

let computeBtn = document.querySelector(".compute-btn");

function calc() {
  let total = 0;
  grades.forEach((grade) => {
    total += grade;
  });

  let gpa = total / grades.length; // Calculate average GPA
  alert("Your GPA is: " + gpa.toFixed(2)); // Show GPA with 2 decimal points
}

computeBtn.addEventListener("click", calc);
