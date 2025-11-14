<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Manual</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #ffffff;
      color: #333;
      font-family: 'Segoe UI', sans-serif;
    }

    /* Sidebar */
    .sidebar {
      border-right: 1px solid #ddd;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      padding-top: 30px;
      background-color: #f9f9f9;
    }

    .sidebar h5 {
      font-weight: 600;
      text-align: center;
      margin-bottom: 1rem;
       padding: 8px 16px;
       background-color: #007bff; 
       color: #ffffffff;
    }

    .sidebar a {
      text-decoration: none;
      color: #333;
      display: block;
      padding: 8px 16px;
      border-radius: 5px;
    }

    .sidebar a:hover {
      background-color: #007bff;
      color: #fff;
    }

    .sidebar .btn-back {
        background-color: #007bff;
        color: #fff;
        font-weight: 500;
        border: none;
        display: block;
        margin: 0 auto;
        width: 75%;
        text-align: center;
    }

    .sidebar .btn-back:hover {
      background-color: #0069d9;
    }

    /* Collapsible lists */
    .sidebar ul {
      list-style: none;
      padding-left: 0;
    }

    .sidebar ul li {
      margin: 10px 0;
    }

    .sidebar ul li ul {
      padding-left: 20px;
      margin-top: 5px;
    }

    .sidebar ul li ul li a {
      font-size: 14px;
      color: #555;
      padding: 4px 10px;
    }

    .sidebar ul li ul li a:hover {
      background-color: #007bff;
      color: #fff;
    }

    .sidebar a[aria-expanded="true"] span {
      transform: rotate(180deg);
    }

    /* Main content */
    .content {
      margin-left: 270px;
      padding: 40px;
    }

    .content h2 {
      font-weight: 700;
    }

    .summary-box {
      background-color: #f5f9ff;
      border-left: 5px solid #007bff;
      padding: 15px 20px;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="sidebar">
<!-- Back Button -->
<div class="text-center mb-4">
  <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-back w-75">
    <i class="fas fa-arrow-left me-2"></i> Back to System
  </a>
</div>




   <h5>How to use</h5>
  <ul class="list-unstyled">

    <!-- Dashboard Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#dashboardMenu" role="button"
         aria-expanded="false" aria-controls="dashboardMenu">
        Dashboard
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="dashboardMenu">
        <li><a href="#overview">• Overview</a></li>
        <li><a href="#stats">• Activity Summary</a></li>
      </ul>
    </li>

    <!-- Files Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#filesMenu" role="button"
         aria-expanded="false" aria-controls="filesMenu">
        Files
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="filesMenu">
        <li><a href="#file-structure">• View Files</a></li>
        <li><a href="#file-management">• Organize Files</a></li>
      </ul>
    </li>

    <!-- Shared Files Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#sharedMenu" role="button"
         aria-expanded="false" aria-controls="sharedMenu">
        Shared Files
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="sharedMenu">
        <li><a href="#share-files">• Share Files</a></li>
        <li><a href="#permissions">• Manage Access</a></li>
      </ul>
    </li>

    <!-- Manage Upload Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#uploadMenu" role="button"
         aria-expanded="false" aria-controls="uploadMenu">
        Manage Upload
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="uploadMenu">
        <li><a href="#approve">• Approve Uploads</a></li>
        <li><a href="#standards">• File Standards</a></li>
      </ul>
    </li>

    <!-- Request Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#requestMenu" role="button"
         aria-expanded="false" aria-controls="requestMenu">
        Request
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="requestMenu">
        <li><a href="#make-request">• Make a Request</a></li>
        <li><a href="#approve-request">• Approve Requests</a></li>
      </ul>
    </li>

    <!-- Categories Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#categoryMenu" role="button"
         aria-expanded="false" aria-controls="categoryMenu">
        Categories
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="categoryMenu">
        <li><a href="#create-category">• Create Category</a></li>
        <li><a href="#manage-category">• Manage Categories</a></li>
      </ul>
    </li>

  </ul>


   <h5>Glossary</h5>
  <ul class="list-unstyled">
    <li><a href="#term1">• Term 1</a></li>
    <li><a href="#term2">• Term 2</a></li>
    <li><a href="#term3">• Term 3</a></li>
  </ul>

    <h5>Contact</h5>
  <ul class="list-unstyled">
    <li><a href="#support">• Support</a></li>
    <li><a href="#feedback">• Feedback</a></li>
    <li><a href="#contact">• About archiving system</a></li>
  </ul>


  

</div>

<!-- Main Content -->
<div class="content">
  <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid mb-3">
  <h2>Welcome to the Archiving System Manual</h2>
  <p class="text-muted">Tutorial for Admin ·</p>
  <hr>

  <div class="summary-box mb-4">
    <h5>Summary</h5>
    <p>
     This manual provides a comprehensive guide for users of the Web-Based Archiving System designed for the 
     Records Section of the Administrative Unit, DepEd Ozamiz City. <br>It serves as a step-by-step reference to 
     help users efficiently navigate and utilize the system’s features while ensuring proper digital record management.
    </p>
  </div>

 
<h1 class="title">Dashboard</h1>

<!-- ============================
     DASHBOARD
============================= -->
<section id="dashboard" class="mb-5">
  <h4>Dashboard Overview</h4>

  <h5>Step 1: Understanding the Dashboard</h5>
  <p>The Dashboard serves as the main landing area where users get a quick summary of activities happening in the system. It includes an overview of uploads, requests, and recent file interactions. This helps users stay aware of the most important updates at a glance.</p>
  <p>The goal of the Dashboard is to simplify navigation by presenting the most relevant information in a compact layout. Users can quickly see what needs attention without browsing through multiple menus.</p>
  <p>By centralizing these insights, the Dashboard boosts productivity and ensures users remain informed about the latest system activity.</p>
  <img src="images/dashboard_overview.jpg" alt="Dashboard Overview" class="step-image">

</section>

<!-- ============================
     FILES SECTION
============================= -->
<section id="file-structure" class="mb-5">
  <h4>Files</h4>

  <h5>Step 1: Managing Your Files</h5>
  <p>The Files module allows users to upload, organize, and edit documents under different folders and categories. This helps maintain a clean structure and makes searching easier and faster.</p>
  <p>Users can create folders, rename them, and categorize documents based on their type or purpose. Proper organization ensures smoother workflows, especially when dealing with large file collections.</p>
  <p>This section acts as the storage center of the system, where all documents are maintained and prepared for sharing or archiving.</p>
  <img src="images/files_section.jpg" alt="Files Section" class="step-image">

</section>

<!-- ============================
     SHARED FILES
============================= -->
<section id="sharedfiles" class="mb-5">
  <h4>Shared Files</h4>

  <h5>Step 1: Accessing Shared Documents</h5>
  <p>The Shared Files page displays all files that users have shared or received access to. This helps streamline collaboration by allowing designated individuals to access documents without needing direct file transfers.</p>
  <p>Users can manage permissions, update access settings, and monitor who has interacted with the files. This ensures better control over sensitive information.</p>
  <p>The Shared Files section makes teamwork faster and more secure by centralizing all shared content in one place.</p>
  <img src="images/shared_files.jpg" alt="Shared Files Overview" class="step-image">

</section>

<!-- ============================
     MANAGE UPLOAD
============================= -->
<section id="manageupload" class="mb-5">
  <h4>Manage Upload</h4>

  <h5>Step 1: Reviewing Uploaded Files</h5>
  <p>This section is designed for admins who oversee file quality, accuracy, and compliance before final storage. It ensures that only valid and approved files remain in the system.</p>
  <p>Admins can view pending uploads, verify their contents, and either approve or reject them based on guidelines. This helps preserve data integrity across the entire archive.</p>
  <p>Manage Upload is essential for maintaining a clean and reliable document database.</p>
  <img src="images/manage_upload.jpg" alt="Manage Upload Section" class="step-image">

</section>

<!-- ============================
     FILE REQUESTS
============================= -->
<section id="request" class="mb-5">
  <h4>Request</h4>

  <h5>Step 1: Requesting File Access</h5>
  <p>Users can request access to files that are secured or stored under restricted categories. This ensures proper authorization before sensitive files are shared.</p>
  <p>Once a request is made, it goes into a review process where admins evaluate its purpose and legitimacy. Approved files automatically appear under the user's Shared Files list.</p>
  <p>This workflow provides a controlled environment for accessing important documents while maintaining security.</p>
  <img src="images/request_file.jpg" alt="File Request Page" class="step-image">

</section>

<!-- ============================
     CATEGORIES SECTION
============================= -->
<section id="categories" class="mb-5">
  <h4>Categories</h4>

  <h5>Step 1: Organizing Through Categories</h5>
  <p>Categories help in grouping files for better structure and search accuracy. Users can filter documents, locate folders faster, and understand where each file belongs.</p>
  <p>Admins can create, edit, or remove categories depending on organizational needs. This flexibility ensures the system grows along with the archive's size and complexity.</p>
  <p>By using categories effectively, file retrieval becomes significantly easier and more efficient.</p>
  <img src="images/categories.jpg" alt="Categories Page" class="step-image">

</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
