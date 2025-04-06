/*=========================================================================================
    File Name: app-file-manager.js
    Description: app-file-manager js
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

// all the elements references
var sidebarFileManager = $('.sidebar-file-manager')
var sidebarToggler = $('.file-manager-toggler');
var fileManagerOverlay = $('.file-manager-content-overlay');
var sidebarRight = $('.right-sidebar');

// sidebar file manager list scrollbar
if ($('.list').length > 0) {
  var sidebarLeftList = new PerfectScrollbar('.list', {
    suppressScrollX: true
  });
}

// right-content-wrapper and rightside bar scrollbar 
if ($('.file-manager-main-content .right-sidebar').length > 0) {
  var rightsidebar = new PerfectScrollbar('.file-manager-main-content .right-sidebar .card-body', {
    suppressScrollX: true,
    wheelPropagation: false
  });
}

if ($('.file-manager-main-content').length > 0) {
  var rightContentWrapper = new PerfectScrollbar('.file-manager-main-content .bottom-content', {
    cancelable: true,
    wheelPropagation: false
  });
}

// click event for show sidebar
sidebarToggler.on("click", function () {
  sidebarFileManager.toggleClass('show');
  fileManagerOverlay.toggleClass('show');
})

// remove sidebar
$(".file-manager-content-overlay, .sidebar-close-icon").on("click", function () {
  sidebarFileManager.removeClass("show");
  fileManagerOverlay.removeClass("show");
  sidebarRight.removeClass('show')
})


// On click of "app-file-info and files-info" opening right sidebar
$('.folder-info, .files-info').on('click', function (e) {
  if (!e.target.classList.contains('icon-more-vertical') && !e.target.classList.contains('media-object')) {
    sidebarRight.addClass('show');
    fileManagerOverlay.addClass('show');
  }
});

// opening right sidebar on click of a table row
$('#data-list-view tbody > tr').on('click', function (e) {
  if (!e.target.classList.contains('icon-more-vertical')) {
    sidebarRight.addClass('show');
    fileManagerOverlay.addClass('show');
  }
});


// on screen Resize remove .show from overlay and sidebar
$(window).on('resize', function () {
  if ($(window).width() > 768) {
    if (fileManagerOverlay.hasClass('show')) {
      sidebarFileManager.removeClass('show');
      fileManagerOverlay.removeClass('show');
      sidebarRight.removeClass('show');
    }
  }
})

// making active to list item in links on click
  $(".file-manager-application .sidebar-menu-list .list-group a").on('click', function(){
    if($('.file-manager-application .sidebar-menu-list .list-group a').hasClass('active')){
      $('.file-manager-application .sidebar-menu-list .list-group a').removeClass('active');
    }
    $(this).addClass("active");
  });

// init list view datatable
var dataListView = $(".data-list-view").DataTable({
  responsive: false,
  scrollCollapse: true,
  paging: false,
  searching: false,
  ordering: false,
  select: true,
  info: false,
  select: {
    style: 'single'
  }
});
