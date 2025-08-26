<?php 

$main_page = (basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php') ? './php' : '.';

$style_path = (basename($_SERVER['SCRIPT_NAME']) == 'dashboard.php') ? './style' : '../style';
?>

<link rel="stylesheet" href="<?php echo $style_path; ?>/sidebar.css">

<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo $main_page == './php' ? './dashboard.php' : '../dashboard.php'; ?>" 
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $main_page == './php' ? './php/dashboard.students.php' : './dashboard.students.php'; ?>"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.students.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Students</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $main_page == './php' ? './php/dashboard.courses.php' : './dashboard.courses.php'; ?>"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.courses.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $main_page == './php' ? './php/dashboard.settings.php' : './dashboard.settings.php'; ?>"
               class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</aside>