<div class="main-nav">
     <!-- Sidebar Logo -->
     <div class="logo-box">
          <a href="{{ route('dashboard')}}" class="logo-dark">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logo-dark.png" class="logo-lg" alt="logo dark">
          </a>

          <a href="{{ route('dashboard')}}" class="logo-light">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logo-light.png" class="logo-lg" alt="logo light">
          </a>
     </div>

     <!-- Menu Toggle Button (sm-hover) -->
     <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
          <i class="ri-menu-2-line fs-24 button-sm-hover-icon"></i>
     </button>

     <div class="scrollbar" data-simplebar>

          <ul class="navbar-nav" id="navbar-nav">

               <li class="menu-title">القائمة الرئيسية</li>

               <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard')}}">
                         <span class="nav-icon">
                              <i class="ri-dashboard-2-line"></i>
                         </span>
                         <span class="nav-text">لوحة التحكم</span>
                    </a>
               </li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarInvoices" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarInvoices">
                         <span class="nav-icon">
                              <i class="ri-file-text-line"></i>
                         </span>
                         <span class="nav-text">الفواتير</span>
                    </a>
                    <div class="collapse" id="sidebarInvoices">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('invoices.index')}}">الفواتير المتاحة</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('invoices.create')}}">شراء فاتورة جديدة</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('invoices.sold')}}">الفواتير المباعة</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('customers.index')}}">العملاء</a>
                              </li>
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link" href="{{ route('capital.index')}}">
                         <span class="nav-icon">
                              <i class="ri-money-dollar-circle-line"></i>
                         </span>
                         <span class="nav-text">رأس المال</span>
                    </a>
               </li>

               <li class="nav-item">
                    <a class="nav-link" href="{{ route('investors.index')}}">
                         <span class="nav-icon">
                              <i class="ri-group-line"></i>
                         </span>
                         <span class="nav-text">المستثمرين</span>
                    </a>
               </li>

          </ul>
     </div>
</div>
