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
      z-index: 1030;
      transition: transform 0.3s ease-in-out;
      padding-top: 30px;
      background-color: #f9f9f9;
    }

    .sidebar h3 {
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
    .step-image {
        width: 100%;
        max-width: 1000px; /* Limit max width for very large screens */
        margin-bottom: 50px;
    }

    /* Responsive styles */
    .sidebar-toggle {
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1031; /* Above sidebar */
      display: none; /* Hidden by default */
    }



    @media (max-height: 900px) {
      .sidebar {
        padding-top: 15px;
        overflow-y: auto; /* Add scroll for very short heights */
      }
      .sidebar h3 {
        padding: 4px 8px;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
      }
      .sidebar .btn-back {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
      }
      .sidebar a {
        padding: 4px 8px;
        font-size: 0.9rem;
      }
      .sidebar ul li {
        margin: 5px 0;
      }
      .sidebar ul li ul {
        padding-left: 10px;
      }
      .sidebar ul li ul li a {
        font-size: 12px;
        padding: 2px 5px;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar Toggle Button (for mobile) -->
<button class="btn btn-primary sidebar-toggle" type="button" aria-label="Toggle navigation">
  <i class="fas fa-bars"></i>
</button>

<div class="sidebar">
<!-- Back Button -->
<div class="text-center mb-4">
  <a href="#" class="btn btn-sm btn-close-sidebar d-lg-none" style="position: absolute; top: 10px; right: 10px; font-size: 1.2rem; color: #333;">&times;</a>
  <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-back w-75">
    <i class="fas fa-arrow-left me-2"></i> Back to System
  </a>
</div>




   <h3 id="sidebarAccordion">How to use</h3>
    
  <ul class="list-unstyled">

    <!-- Dashboard Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#dashboardMenu" role="button"
         aria-expanded="false" aria-controls="dashboardMenu">
        Dashboard
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="dashboardMenu" data-bs-parent="#sidebarAccordion">
        <li><a href="#dashboard-understanding">• Understanding the Dashboard</a></li>
        <li><a href="#dashbfunc">• Dashboard functions</a></li>
      </ul>
    </li>
        <!-- Files Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#filesMenu" role="button"
         aria-expanded="false" aria-controls="filesMenu">
        Files Page
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="filesMenu" data-bs-parent="#sidebarAccordion">
        
        <li><a href="#files-overview-h3">• File page Overview</a></li>
        <li><a href="#files-upload">• Uploading File</a></li>
        <li><a href="#files-view">• View a File</a></li>
        <li><a href="#files-download">• Download a File</a></li>
        <li><a href="#files-edit">• Edit a File</a></li>
        <li><a href="#files-search-h3">• Search Files</a></li>
        <li><a href="#files-tabs-h3">• File Tabs</a></li>

      </ul>
    </li>

    <!-- Folder Dropdown -->
    <li>
      <a class="d-flex justify-content-between align-items-center"
         data-bs-toggle="collapse" href="#folderMenu" role="button"
         aria-expanded="false" aria-controls="folderMenu">
        Folder Structure
        <span>&#9662;</span>
      </a>
      <ul class="collapse" id="folderMenu" data-bs-parent="#sidebarAccordion" style="scroll-behavior: smooth;">
        <li><a href="#addmain">• Adding Main Folder</a></li>
        <li><a href="#deletemain">• Deleting Main Folder</a></li>
        <li><a href="#addsub">• Adding SubFolder</a></li>
        <li><a href="#deletesub">• Deleting SubFolder</a></li>
        <li><a href="#search-main-folder">• Search Main Folder</a></li>
        <li><a href="#search-sub-folder">• Search SubFolder</a></li>
  

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
      <ul class="collapse" id="sharedMenu" data-bs-parent="#sidebarAccordion">
        <li><a href="#shared-documents">• Shared Documents Overview</a></li>
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
      <ul class="collapse" id="uploadMenu" data-bs-parent="#sidebarAccordion">
        <li><a href="#reviewing-uploads">• Uploads Overview</a></li>
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
      <ul class="collapse" id="requestMenu" data-bs-parent="#sidebarAccordion">
        <li><a href="#requesting-access">• Request Overview</a></li>
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
      <ul class="collapse" id="categoryMenu" data-bs-parent="#sidebarAccordion">
        <li><a href="#organizing-categories">• Categories Overview</a></li>
        <li><a href="#manage-category">• Manage Categories</a></li>
      </ul>
    </li>

  </ul>


    <h3>Contact</h3>
  <ul class="list-unstyled">
      <li><a href="#contact">• About archiving system</a></li>
    <li><a href="#contact-support">• Contac & Support</a></li>
  
  </ul>


  

</div>

<!-- Main Content -->
<div class="content">
  <img src="/cdi/deped/public/uploads/pics/deped-ozamiz-2.png" alt="Logo" class="img-fluid mb-3">
  <h2 id="home">Welcome to the Archiving System Manual</h2>
  <p class="text-muted">Tutorial for Admin ·</p>
  <hr>

  <div class="summary-box mb-4">
    <h3>Summary</h3>
    <p>
     This manual provides a comprehensive guide for users of the Web-Based Archiving System designed for the 
     Records Section of the Administrative Unit, DepEd Ozamiz City. <br>It serves as a step-by-step reference to 
     help users efficiently navigate and utilize the system’s features while ensuring proper digital record management.
    </p>
  </div>

 


<!-- ============================
     DASHBOARD
============================= -->
<section id="overview" class="mb-5" >
    <h2 class="title">Dashboard</h2>
    <h3 id="dashboard-understanding">Step 1: Understanding the Dashboard</h3>
    <p>The Dashboard serves as the main landing area where users get a quick summary of activities happening in the system.</p>
    
    <img src="/cdi/deped/public/uploads/pics/manual/admindashboard.png" alt="Dashboard Overview" class="step-image">
    <h3 class="mt-4" id="dashbfunc">Dashboard functions</h3>
    <p>On the dashboard, first thing you see is the three summary cards: <strong>Total Files</strong>, <strong>New Uploaded Files</strong>, and <strong>Pending Requests</strong>. <br>
    These cards provide a quick overview of file activity and can be clicked to view more details, or you can access the same pages via the <strong>navigation bar on the left.</strong></p>
    <img src="/cdi/deped/public/uploads/pics/manual/admindashboard1.png" alt="Dashboard Overview" class="step-image">
</section>

  <!-- ============================
       FILES SECTION
  ============================= -->
  <hr class="my-4">
  <section id="files-overview" class="mb-5">
    <h2 class="title">Files</h2>

    <h3 id="files-overview-h3">Files Overview</h3>
    <p>After navigating to the second subfolder (the last folder), you will see the file management area. <br>
    Here,This is where you can upload, organize, and manage documents within different folders and categories.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/inside3folder.png" alt="Dashboard Overview" class="step-image">
  
    <h3 id="files-upload">Uploading a File</h3>
    <p><strong>Step 1:</strong> Click the <strong>Upload</strong> button that is highlited in the picture below. <br>
                The upload form will then be displayed for you to enter the required details and select the file to upload.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/inside3folder1.png" alt="Dashboard Overview" class="step-image">


    <p><strong>Step 2:</strong> After clicking the <strong>Upload</strong> button, the upload form will be displayed.
     Fill out the form with the required information,<br>
    then click the <strong>Upload</strong> button or press <strong>ENTER</strong> on your keyboard to upload the file and then the file will be uploaded succesfully.</p> 
    <strong>Note:</strong> You can upload file types such as PDF, DOCX, XLSX, or PPT, based on whether these settings have been enabled by the superadmin.  However, 
      <br>it is recommended to use PDF files in accordance with system policy. If you are unable to upload DOCX, XLSX, 
      or PPT files, please contact the IT administrator for assistance.</p>
   <img src="/cdi/deped/public/uploads/pics/manual/clickupload1.png" alt="Dashboard Overview" class="step-image">
   <p><strong>Note: </strong>After clicking <Strong>Upload</Strong>, a message will appear confirming that the file was uploaded successfully. 
    The file will then show a status of <strong> Pending</strong> for Review, <br> 
    which means it is awaiting approval or verification by the Superadmin before it becomes accessible in the system. This ensures that all files meet system requirements and policies.</p>
    <img src="/cdi/deped/public/uploads/pics/manual/uploadsuccess.png" alt="Dashboard Overview" class="step-image">



    <h3 id="files-view">View and Print file</h3>
    <p><strong>Step 1:</strong> Click the <strong>View</strong> button that is highlighted in the picture below. <br>
               it will open the file into other tab.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/clickview1.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Step 2:</strong> Inside the <strong>View</strong> page, you can check the contents of the file. If you want to print or download it, look for the highlighted buttons shown in the image below. <br>
  You can click either the <strong>Print or Download</strong> .</p>
  <img src="/cdi/deped/public/uploads/pics/manual/view1.png" alt="Dashboard Overview" class="step-image">
</section>

    <h3 id="files-download">Download file</h3>
    <p><strong>Step 1:</strong> Click the <strong>Download</strong> button that is highlighted in the picture below. </p>
  <img src="/cdi/deped/public/uploads/pics/manual/downloadclick.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Step 2:</strong> After clicking the <strong>Download</strong> button, you will see the file downloading 
  at the top-right corner of your browser. If a security prompt appears, click <strong>Keep</strong> to continue the download.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/clickkeep.png" alt="Dashboard Overview" class="step-image">

  <p><strong>Step 3:</strong>You can check your downloaded file by clicking the <strong>Download </strong>icon at the top of the browser or by opening the  <strong> menu (three dots) and select Downloads</strong> </p>
  <img src="/cdi/deped/public/uploads/pics/manual/downloadfind.png" alt="Dashboard Overview" class="step-image">
</section>

    <h3 id="files-edit">Edit file</h3>
    <p><strong>Step 1:</strong> Click the <strong>Edit</strong> button that is highlighted in the picture below. </p>
  <img src="/cdi/deped/public/uploads/pics/manual/edit1.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Step 2:</strong> After clicking the <strong>Edit</strong> button, A form will display,then rename and click submit to save</p>
  <img src="/cdi/deped/public/uploads/pics/manual/renameform.png" alt="Dashboard Overview" class="step-image">

  <h3 id="files-search-h3">Search file</h3>
  <p><strong>Step 1:</strong> Click the <strong>Search</strong> input that is highlighted in the picture below and type the file name you want to search, then click the <strong>Search</strong> button. </p>
  <img src="/cdi/deped/public/uploads/pics/manual/search1.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Step 2:</strong> After clicking the <strong>search</strong> button, the files table will dispaly the file that you've searched</p>
  <img src="/cdi/deped/public/uploads/pics/manual/searchclear.png" alt="Dashboard Overview" class="step-image">
   <p><strong>Step 3:</strong> Clearing the <strong>Search</strong> input will display all the files</p>
  <img src="/cdi/deped/public/uploads/pics/manual/searchclear1.png" alt="Dashboard Overview" class="step-image">

  <h3 id="files-tabs-h3">File Tabs</h3>
  <p>The file section contains three tabs: <strong>Active</strong>, <strong>Archive</strong>, and <strong>Expired</strong>. The <strong>Active</strong> 
  tab displays all files that are currently available and approved for use. The <strong>Archive</strong> tab stores files that have been officially archived for record-keeping but are no longer part of daily operations. The <strong>Expired</strong> tab contains files that have passed their validity period or 
  retention schedule, indicating that they are no longer current and may require review, renewal, or disposal based on system policies.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/filetabspng.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Note: </strong> In the <strong>Archive</strong> tab, you can request access to archived files by filling out the request form and clicking <strong>Send</strong>. 
  Your request will be forwarded to the Superadmin, who will review it and provide the file through the Requests page.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/archivetab.png" alt="Dashboard Overview" class="step-image">
  <p><strong>Note: </strong>In the <strong>Delete</strong> tab, you can manually delete all files listed under the <strong>Expired</strong> 
  tab by clicking the delete button. After deleting, you can view the complete list of removed files in the <strong>
    Deleted Logs</strong> section, located beside the file tabs menu.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/expiretab.png" alt="Dashboard Overview" class="step-image">

</section>

<!-- ============================
     FOLDER STRUCTURE
============================= -->
<hr class="my-4">
<section id="folder-structure-section" class="mb-5">
  <h2>FOLDER STRUCTURE</h2>

  <h4 id="addmain">Adding Main Folder</h4>
  <p>To add a new main folder, navigate to the Files Page  and click the "AddFolder" button. </p>

   <img src="/cdi/deped/public/uploads/pics/manual/adminfile1.png" alt="Add Main Folder" class="step-image"> 
   <p>Enter the desired name for your new main folder and confirm. This creates a top-level organizational unit for your files.</p>
   <img src="/cdi/deped/public/uploads/pics/manual/addfolderform.png" alt="Add Main Folder" class="step-image"> 

  <h4 id="deletemain">Deleting Main Folder</h4>
  <p>To delete a main folder, locate the folder you wish to remove in the folder management interface. Click the "Delete" icon (often a trash can) next to the folder name and confirm the deletion. Be cautious, as deleting a main folder will typically also delete all its subfolders and files within it. </p>
  <p><strong>Note:</strong> A main folder cannot be deleted if it is assigned to a user or contains subfolders and files inside.</p>
 <img src="/cdi/deped/public/uploads/pics/manual/adminfile2.png" alt="Delete Main Folder" class="step-image"> 

  <h4 id="addsub">Adding SubFolder</h4>
  <p>To add a subfolder, first navigate into the Main folder where you want to create it. Then, click the "Add " button, <br>
  Enter the subfolder's name, and save. Subfolders help in further organizing files within a main folder.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/addsubfolder.png" alt="Delete Main Folder" class="step-image"> 

  <h4 id="deletesub">Deleting SubFolder</h4>
  <p>To delete a subfolder, navigate to the subfolder within its Main Folder. Click the "Delete" icon associated with the subfolder and confirm the action.</p>
  <p><strong>Note: </strong>You Cannot Delete Subfolder When its has A files inside, you must delete all the files inside</p>
  <!-- You might want to add an image here: <img src="/cdi/deped/public/uploads/pics/manual/delete_sub_folder.jpg" alt="Delete SubFolder" class="step-image"> -->

  

</section>

<!-- ============================
     SHARED FILES
============================= -->
<hr class="my-4">
<section id="shared-files-section" class="mb-5">
  <h4>Shared Files</h4>

  <h5 id="shared-documents">Shared Documents Overview</h5>
  <p>The Shared Files page displays all files that users have shared or received access to. This helps streamline collaboration by allowing designated individuals to access documents without needing direct file transfers.</p>
  <p>Users can manage permissions, update access settings, and monitor who has interacted with the files. This ensures better control over sensitive information.</p>
  <p>The Shared Files section makes teamwork faster and more secure by centralizing all shared content in one place.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/shared_files.jpg" alt="Shared Files Overview" class="step-image">

  <h3 id="permissions">Manage Access</h3>
  <p>This section allows administrators to manage access permissions for shared files. You can grant or revoke access to specific users or groups, set expiration dates for access, and review audit logs of file interactions. This ensures that sensitive documents are only accessible to authorized personnel.</p>
  <!-- You might want to add an image here: <img src="/cdi/deped/public/uploads/pics/manual/manage_permissions.jpg" alt="Manage Access" class="step-image"> -->

</section>

<!-- ============================
     MANAGE UPLOAD
============================= -->
<hr class="my-4">
<section id="approve" class="mb-5">
  <h4>Manage Upload</h4>

  <h3 id="reviewing-uploads">Uploads Overview</h3>
  <p>This section is designed for admins who oversee file quality, accuracy, and compliance before final storage. It ensures that only valid and approved files remain in the system.</p>
  <p>Admins can view pending uploads, verify their contents, and either approve or reject them based on guidelines. This helps preserve data integrity across the entire archive.</p>
  <p>Manage Upload is essential for maintaining a clean and reliable document database.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/manage_upload.jpg" alt="Manage Upload Section" class="step-image">

</section>

<!-- ============================
     FILE REQUESTS
============================= -->
<hr class="my-4">
<section id="make-request" class="mb-5">
  <h2>Request</h2>

  <h4 id="requesting-access">Request Overview</h4>
  <p>Users can request access to files that are secured or stored under restricted categories. This ensures proper authorization before sensitive files are shared.</p>
  <p>Once a request is made, it goes into a review process where admins evaluate its purpose and legitimacy. Approved files automatically appear under the user's Shared Files list.</p>
  <p>This workflow provides a controlled environment for accessing important documents while maintaining security.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/request_file.jpg" alt="File Request Page" class="step-image">

</section>

<!-- ============================
     CATEGORIES SECTION
============================= -->
<section id="create-category" class="mb-5">
  <hr class="my-4">
  <h4>Categories</h4>

  <h3 id="organizing-categories">Categories Overview</h3>
  <p>Categories help in grouping files for better structure and search accuracy. Users can filter documents, locate folders faster, and understand where each file belongs.</p>
  <p>Admins can create, edit, or remove categories depending on organizational needs. This flexibility ensures the system grows along with the archive's size and complexity.</p>
  <p>By using categories effectively, file retrieval becomes significantly easier and more efficient.</p>
  <img src="/cdi/deped/public/uploads/pics/manual/categories.jpg" alt="Categories Page" class="step-image">

</section>

<!-- ============================
     CONTACT SECTION
============================= -->
<section id="contact-section" class="mb-5">
  <hr class="my-4">
  
<h3 id="contact">About the Archiving System</h3>
<p>This system was developed by the DevTeam of the BS Information Technology program at La Salle University Ozamiz as a Capstone Project. 
  The team is composed of three members: Keane Maundrey A. Egbus, Jigger C. Sebarios, and Mia Abigail Torres. The project aims to modernize and streamline the document management process for the DepEd Ozamiz City Records Section.</p>

  <h3 id="contact-support">Support</h3>
  <p>If you encounter any technical issues or have questions about using the system, please contact the IT administrator for assistance.</p>
  <strong>DepEd Ozamiz City – IT administrator</strong><br> Email: <a href="mailto:regie.catedral@deped.gov.ph">regie.catedral@deped.gov.ph</a><br> Contact Person: Regie Catedral<br> 
  Location: 5R5Q+CV7, Mayor Benjamin Alinas Fuentes Avenue, Ozamiz City, Misamis Occidental</p>



</section>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // The script for a back-to-top link was here, but the link itself
  // was not found in the HTML. I have replaced it with a script for the responsive sidebar.
  document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const closeButton = document.querySelector('.btn-close-sidebar');

    function toggleSidebar() {
      sidebar.classList.toggle('active');
      // Optional: push content when sidebar is active on mobile
      // content.classList.toggle('sidebar-active'); 
    }

    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', toggleSidebar);
    }
    if (closeButton) {
      closeButton.addEventListener('click', toggleSidebar);
    }
  });
</script>
</body>
</html>
