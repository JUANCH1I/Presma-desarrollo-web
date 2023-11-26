<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>olimpiadas</title>
</head>
<body>
<nav class="sidebar locked">
      <div class="logo_items flex">
        <span class="nav_image">
          <img src="images/logo.png" alt="logo_img" />
        </span>
        <span class="logo_name">Presma</span>
        <i class="bx bx-lock-alt" id="lock-icon" title="Unlock Sidebar"></i>
        <i class="bx bx-x" id="sidebar-close"></i>
      </div>
      <div class="menu_container">
        <div class="menu_items">
          <ul class="menu_item">
            <li class="item">
              <a href="visual.php" class="link flex">
                <i class="bx bx-home-alt"></i>
                <span>Visual</span>
              </a>
            </li>
            <li class="item">
              <a href="abm.php" class="link flex">
                <i class="bx bx-home-alt"><svg xmlns="http://www.w3.org/2000/svg" width="47" height="42" viewBox="0 0 47 42" fill="none">
<path d="M23.3333 0C16.9167 0 11.6667 5.88 11.6667 13.125C11.6667 20.37 16.9167 26.25 23.3333 26.25C29.75 26.25 35 20.37 35 13.125C35 5.88 29.75 0 23.3333 0ZM11.1417 26.25C4.95833 26.5125 0 31.08 0 36.75V42H46.6667V36.75C46.6667 31.08 41.7667 26.5125 35.525 26.25C32.375 29.4525 28.0583 31.5 23.3333 31.5C18.6083 31.5 14.2917 29.4525 11.1417 26.25Z" fill="black"/>
</svg></i>
                <span>Prestamos</span>
              </a>
            </li>
            <li class="item">
              <a href="abmPersonas.php" class="link flex">
                <i class="bx bx-grid-alt"></i>
                <span>Usuarios</span>
              </a>
            </li>
            <li class="item">
              <a href="#" class="link flex">
                <i class="bx bx-grid-alt"></i>
                <span>Zonas</span>
              </a>
            </li>
            <li class="item">
              <a href="#" class="link flex">
                <i class="bx bx-grid-alt"></i>
                <span>Personal</span>
              </a>
            </li>
          </ul>
        </div>
        <div class="sidebar_profile flex">
          <span class="nav_image">
            <img src="images/profile.jpg" alt="logo_img" />
          </span>
          <div class="data_text">
            <span class="name"><?php echo $_SESSION["user_name"] ?></span>
            <span class="email"><?php echo $_SESSION["user_rol"] ?></span>
          </div>
        </div>
      </div>
    </nav>
