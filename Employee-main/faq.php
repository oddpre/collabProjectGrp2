<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ | WORK EASE</title>
  <link rel="icon" href="/img/nkey.ico">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;600;800&display=swap" rel="stylesheet">
  <link rel="icon" type="image/png" href="./img/favicon.png">
  <link rel="stylesheet" href="./css/style.css">
</head>
<body>
  <div class="container_fag">
    <h2 class="dashboard_H2"> Frequently Asked Questions</h2>

    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h2 class="accordion-header" id="q1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1" aria-expanded="true">
            How do I clock in or out?
          </button>
        </h2>
        <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                   <div class="accordion-body">
            Use the "⏱ Clock In/Out" button on the dashboard or front page. If you forget to clock out, the system automatically sets clock out to 8 hours later, only if the day has passed.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
            Why does my timesheet show "Absent"?
          </button>
        </h2>
        <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            You are marked as absent if there is no clock-in or vacation request for that day. Sundays are never marked absent.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">
            How do I apply for vacation?
          </button>
        </h2>
        <div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Go to "My Vacation" from the menu. Select the date range and write a short reason. Your request will be marked as pending until admin reviews it.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">
            What does "Upcoming" mean in the timesheet?
          </button>
        </h2>
        <div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            Upcoming means the day has not happened yet. It is used instead of "Absent" for future dates.
          </div>
        </div>
      </div>

      <div class="accordion-item">
        <h2 class="accordion-header" id="q5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a5">
            What happens if I forget to clock out?
          </button>
        </h2>
        <div id="a5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            If the current day has passed and you didn’t clock out, the system sets your logout time to 8 hours after your login time, but only after 23:59 that day.
          </div>
        </div>
      </div>
    </div>

    <hr class="my-1">

<h3 class="dashboard_H2">Contact Us</h3>
<form id="contactForm" class="mt-4">
  <div class="mb-1">
    <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
  </div>

  <div class="mb-1">
    <input type="text" class="form-control" id="subject" placeholder="Subject" required>
  </div>

  <div class="mb-1">
  <textarea class="form-control no-resize" id="message" rows="4" placeholder="Your message..." required></textarea>

    <small id="messageHelp" class="form-text text-muted">Minimum 20 characters required.</small>
  </div>
  <div id="contactSuccess" class="alert alert-success mt-3 d-none" style="max-width: 245px; margin: 0 auto;">
  Message sent successfully!
</div>
<div id="contactError" class="alert alert-danger mt-3 d-none" style="max-width: 275px; margin: 0 auto;">
  Please fill out all fields correctly.
</div>
<div class="d-flex justify-content-center align-items-center gap-3 mt-4">
  <button type="submit" class="btn btn-secondary">Send</button>
  <a href="index.html" class="btn btn-secondary">⬅ Back to Home</a>
</div>
</form>

    
  </div>




<script>
  document.getElementById("contactForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const subject = document.getElementById("subject").value.trim();
    const message = document.getElementById("message").value.trim();

    const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    const isSubjectValid = subject.length > 0;
    const isMessageValid = message.length >= 20;

    if (isEmailValid && isSubjectValid && isMessageValid) {
      // Fake send (kan kobles til backend senere)
      document.getElementById("contactSuccess").classList.remove("d-none");
      document.getElementById("contactError").classList.add("d-none");
      document.getElementById("contactForm").reset();
    } else {
      document.getElementById("contactSuccess").classList.add("d-none");
      document.getElementById("contactError").classList.remove("d-none");
    }
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
