      <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
          <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
              <div class="dropdown profile-element">
                <img
                  alt="image"
                  class="rounded-circle"
                  width="50px"
                  src="<?= $_SESSION['profile_picture'] ?>" />
                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                  <span class="block m-t-xs font-bold"><?= $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?></span>
                  <span class="text-muted text-xs block">Manage <b class="caret"></b></span>
                </a>
                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                  <li>
                    <a class="dropdown-item" href="<?= base_url() ?>profile">Profile</a>
                  </li>
                  <li class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="<?= base_url() ?>logout">Logout</a></li>
                </ul>
              </div>
              <div class="logo-element">INV</div>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "product" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>product"><i class="fa fa-diamond"></i>
                <span class="nav-label">Products</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "supplier" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>supplier"><i class="fa fa-truck"></i>
                <span class="nav-label">Suppliers</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "customer" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>customer"><i class="fa fa-users"></i>
                <span class="nav-label">Customers</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "purchase" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>purchase"><i class="fa fa-shopping-cart"></i>
                <span class="nav-label">Purchases</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "purchase_return" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>purchase-return"><i class="fa fa-rotate-left"></i>
                <span class="nav-label">Purchase Returns</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "sales" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>sales"><i class="fa fa-dollar"></i>
                <span class="nav-label">Sales</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "sales_return" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>sales-return"><i class="fa fa-rotate-left"></i>
                <span class="nav-label">Sale Returns</span></a>
            </li>
            <li class="<?= isset($pagename) &&  $pagename == "payments" ? "active" : ""  ?>">
              <a href="<?= base_url() ?>payment"><i class="fa fa-money"></i>
                <span class="nav-label">Payments</span></a>
            </li>
            <li>
              <a href="#"><i class="fa fa-diamond"></i>
                <span class="nav-label">Reports</span></a>
            </li>
          </ul>
        </div>
      </nav>