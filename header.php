<?php
require 'db/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>Solarni Info Sistem</title>

  <link rel="stylesheet" href="css/style.css" />

  <link 
    rel="stylesheet" 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" 
  />

  <script src="js/script.js" defer></script>
</head>
<body>

  <header class="navbar">
    <div class="navbar-container">
      <div class="logo">
        <a href="index.php">SolarniInfoSistem</a>
      </div>

      <div class="nav-toggle">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <nav class="nav-links">
        <ul>
          <li><a href="index.php">Početna</a></li>
          <li><a href="index.php#products">Proizvodi</a></li>
          <li><a href="login.php">Prijava</a></li>
          <li><a href="register.php">Regiistracija</a></li>
        </ul>
      </nav>
    </div>
  </header>
