<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Done</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>

    body {
        margin: 0;
        height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    div {
        background-color:rgba(198, 238, 80, 0.26);
        width: 100%;
        height: 100px;
    }

    h1 {
        padding: 20px;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    img {
        width: 90px;
        height: 90px;
        margin-top: 15px;
        position: relative;
        z-index: 1;
    }

    button {
        width: 600px;
        height: 40px;
        background-color: white;
        font-family: 'Times New Roman', Times, serif;
        font-size: 16px;
        color: black;
        padding: 7px;
        margin-top: 7px;
        border: 2px solid black;
        border-radius: 10px;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }

    button:hover {background-color:rgb(255, 244, 244);}

    button:active {background-color:rgb(228, 228, 228);}

    </style>

</head>


<body>
    
    <div>
        <img class="tick" src="/images/Tick.png" alt="Tick icon">
        <h1>Sign Up Successful</h1>

        <a href="login.php">
            <button type="button">Return to login page</button>
        </a>
    </div>
    
</body>

</html>