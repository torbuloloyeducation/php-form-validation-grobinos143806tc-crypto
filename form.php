<?php

function test_input(string $data): string
{
 
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

$name = $email = $gender = "";
$phone = $website = "";
$termsChecked = false;

$nameErr = $emailErr = $genderErr = "";
$phoneErr = $websiteErr = "";
$passwordErr = $confirmErr = "";
$termsErr = "";

$attempts = 0;
$submittedSuccessfully = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //  maihap ang submission  attempts----
    $attempts = isset($_POST["attempts"]) ? (int)$_POST["attempts"] : 0;
    $attempts++;

    // ---- Validate Name (required) ----
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $nameRaw = trim(stripslashes((string)$_POST["name"]));
        if ($nameRaw === "") {
            $nameErr = "Name is required";
        } else {
            $name = test_input($nameRaw);
        }
    }

    // ---- Validate Email ----
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $emailRaw = trim(stripslashes((string)$_POST["email"]));
        // Keep the user's typed value even if it fails validation.
        $email = ($emailRaw === "") ? "" : test_input($emailRaw);

        if ($emailRaw === "" || !filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // ---- para ma validate ang gender ----
    if (empty($_POST["gender"]) || !in_array($_POST["gender"], ["Male", "Female", "Other"], true)) {
        $genderErr = "Gender is required";
    } else {
        $gender = test_input((string)$_POST["gender"]);
    }

    // ---- Exercise 1: Add a Phone Number Field (Required) ----
    if (empty($_POST["phone_number"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phoneRaw = trim(stripslashes((string)$_POST["phone_number"]));
        $phone = ($phoneRaw === "") ? "" : test_input($phoneRaw);
        $pattern = '/^\+?[0-9\s-]{7,15}$/';
        if ($phoneRaw === "" || !preg_match($pattern, $phoneRaw)) {
            $phoneErr = "Invalid phone format";
        }
    }

    // ---- Exercise 2: Validate the Website Field ----
    if (isset($_POST["website"]) && $_POST["website"] !== "") {
        $websiteRaw = trim(stripslashes((string)$_POST["website"]));
        if ($websiteRaw === "" || !filter_var($websiteRaw, FILTER_VALIDATE_URL)) {
            $websiteErr = "Invalid URL format";
            $website = test_input($websiteRaw);
        } else {
            $website = test_input($websiteRaw);
        }
    }

    // ---- Exercise 3:  Add a Password Field with Confirmation ----
    $passwordRaw = isset($_POST["password"]) ? trim(stripslashes((string)$_POST["password"])) : "";
    $confirmRaw = isset($_POST["confirm_password"]) ? trim(stripslashes((string)$_POST["confirm_password"])) : "";

    if ($passwordRaw === "") {
        $passwordErr = "Password is required";
    } elseif (strlen($passwordRaw) < 8) {
        $passwordErr = "Password must be at least 8 characters long";
    }

    if ($confirmRaw === "") {
        $confirmErr = "Confirm password is required";
    } elseif ($confirmRaw !== $passwordRaw) {
        $confirmErr = "Passwords do not match";
    }

    // ---- Exercise 4: Add a Terms and Conditions Checkbox ----
    $termsChecked = isset($_POST["terms"]);
    if (!$termsChecked) {
        $termsErr = "You must agree to the terms and conditions.";
    }

    // NO ERROR PAG SUCCESS NA
    $hasErrors = ($nameErr !== "" || $emailErr !== "" || $genderErr !== "" ||
        $phoneErr !== "" || $websiteErr !== "" || $passwordErr !== "" ||
        $confirmErr !== "" || $termsErr !== "");

    if (!$hasErrors) {
        $submittedSuccessfully = true;
    }
}

$action = htmlspecialchars($_SERVER["PHP_SELF"] ?? '', ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form Validation</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 24px; }
        .error { color: #b00020; font-size: 0.95em; margin-left: 8px; }
        .field { margin-bottom: 14px; }
        label { display: inline-block; min-width: 150px; }
        input[type="text"], input[type="email"], input[type="url"], input[type="password"] { padding: 6px 8px; width: 260px; }
        .gender-options label { min-width: auto; margin-right: 10px; }
        .success { border: 1px solid #1b5e20; background: #e8f5e9; padding: 12px; margin-top: 16px; }
        .count { font-weight: bold; }
    </style>
</head>
<body>
    <h1>PHP Form Validation</h1>
    <p class="count">Submission attempts: <?php echo (int)$attempts; ?></p>

    <form method="post" action="<?php echo $action; ?>">
        <input type="hidden" name="attempts" value="<?php echo (int)$attempts; ?>">

        <div class="field">
            <label for="name">Name (required)</label>
            <input id="name" type="text" name="name" value="<?php echo $name; ?>">
            <?php if ($nameErr !== ""): ?>
                <span class="error"><?php echo $nameErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="email">Email (required)</label>
            <input id="email" type="email" name="email" value="<?php echo $email; ?>">
            <?php if ($emailErr !== ""): ?>
                <span class="error"><?php echo $emailErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label>Gender (required)</label>
            <div class="gender-options">
                <?php
                $gMale = ($gender === "Male") ? 'checked="checked"' : '';
                $gFemale = ($gender === "Female") ? 'checked="checked"' : '';
                $gOther = ($gender === "Other") ? 'checked="checked"' : '';
                ?>
                <label><input type="radio" name="gender" value="Male" <?php echo $gMale; ?>> Male</label>
                <label><input type="radio" name="gender" value="Female" <?php echo $gFemale; ?>> Female</label>
                <label><input type="radio" name="gender" value="Other" <?php echo $gOther; ?>> Other</label>
            </div>
            <?php if ($genderErr !== ""): ?>
                <span class="error"><?php echo $genderErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="phone_number">Phone Number (required)</label>
            <input id="phone_number" type="text" name="phone_number" value="<?php echo $phone; ?>" placeholder="+1 555-123-4567">
            <?php if ($phoneErr !== ""): ?>
                <span class="error"><?php echo $phoneErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="website">Website (optional)</label>
            <input id="website" type="url" name="website" value="<?php echo $website; ?>" placeholder="https://example.com">
            <?php if ($websiteErr !== ""): ?>
                <span class="error"><?php echo $websiteErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password">
            <?php if ($passwordErr !== ""): ?>
                <span class="error"><?php echo $passwordErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" type="password" name="confirm_password">
            <?php if ($confirmErr !== ""): ?>
                <span class="error"><?php echo $confirmErr; ?></span>
            <?php endif; ?>
        </div>

        <div class="field">
            <label>Terms</label>
            <label style="min-width:auto;">
                <input type="checkbox" name="terms" <?php echo $termsChecked ? 'checked="checked"' : ''; ?>>
                I agree to the terms and conditions
            </label>
            <?php if ($termsErr !== ""): ?>
                <div class="error" style="margin-left: 0;"><?php echo $termsErr; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Submit</button>
    </form>

    <?php if ($submittedSuccessfully): ?>
        <div class="success">
            <h2>Form submitted successfully</h2>
            <p><strong>Name:</strong> <?php echo $name; ?></p>
            <p><strong>Email:</strong> <?php echo $email; ?></p>
            <p><strong>Gender:</strong> <?php echo $gender; ?></p>
            <p><strong>Phone:</strong> <?php echo $phone; ?></p>
            <?php if ($website !== ""): ?>
                <p><strong>Website:</strong> <?php echo $website; ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>

