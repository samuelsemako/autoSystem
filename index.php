<?php include 'config/config.php'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <?php include "meta.php"; ?>
    <title><?php echo $appName ?></title>
</head>

<body>
    <?php include 'alert.php' ?>
    <div class="body-div">
        <div class="body-main">
            <header>
                <div class="header-inner">
                    <div class="logo-div">
                        <img src="<?php echo $websiteUrl ?>/all-images/images/logo.png" alt="logo image">
                    </div>

                    <nav>
                        <ul>
                            <a href="#">
                                <li>Home</li>
                            </a>

                            <a href="#">
                                <li onclick="_viewRecords()">View Record</li>
                            </a>
                        </ul>
                    </nav>
                </div>
            </header>

            <div class="body-content">
                <div class="search-button">
                    <div class="text_field_container" id="searchUser_container">
                        <script>
                            selectField({
                                id: 'searchUser',
                                title: 'Select User'
                            });
                            _getSelectUser('searchUser')
                        </script>
                    </div>

                    <div class="button-div">
                        <button class="btn" onclick="_fetchUser('userId')">
                            Fetch
                        </button>

                        <button class="btn close-btn" onclick="_deleteUser()">
                            Delete
                        </button>
                    </div>
                </div>

                <div class="form-div">
                    <div>
                        <h1>Registration</h1>
                        <div class="text_field_container" id="fullName_container" title="Field for full name">
                            <script>
                                textField({
                                    id: 'fullName',
                                    title: 'Enter Full Name'
                                });
                            </script>
                        </div>

                        <div class="text_field_container" id="emailAddress_container" title="Field for email address">
                            <script>
                                textField({
                                    id: 'emailAddress',
                                    title: 'Enter Email address'
                                });
                            </script>
                        </div>

                        <div class="text_field_container" id="phoneNumber_container" title="Field for phone number">
                            <script>
                                textField({
                                    id: 'phoneNumber',
                                    title: 'Eneter Phone number'
                                });
                            </script>
                        </div>

                        <div class="button-div">
                            <button class="btn" onclick="_submitUser()">Submit</button>
                            <button class="btn close-btn" onclick="_clearFunction()">Clear</button>
                        </div>
                    </div>

                    <div class="passport-div">
                        <h2>Passport</h2>
                        <label>
                            <div class="image-div">
                                <img src="<?php echo $websiteUrl ?>/all-images/images/user.png" alt="Profile pics" id="userPixPreview">
                                <input type="file" id="passport" style="display:none" accept=".jpg, .jpeg, .png, .gif, .bmp, .tiff, .webp, .svg, .avif" onchange="userPixPreview.UpdatePreview(this);" />
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- /////all records Modals/////////////////////////////////////////////////////////////////// -->
    <div class="overlay" id='modal'>
        <div class="loanBreakDownDiv">
            <div class="title-div">
                <h1>User Record</h1>
                <button class="close-btn" onclick="_actionModal('close')">X</button>
            </div>

            <div class="profileTableDiv">
                <div class="profileTable-content">
                    <table id="usersRecord">
                       
                </table>
                </div>

            </div>
        </div>
    </div>
    <!-- //////////////////////////////////////////////////////////////////////////////////////// -->
</body>

</html>