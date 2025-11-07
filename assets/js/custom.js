// SKidiyog Custom JavaScript
// Minimal initialization for Materialize components

$(document).ready(function(){
  // Initialize side navigation
  $('.sidenav').sidenav();

  // Initialize modals
  $('.modal').modal();

  // Initialize select dropdowns
  $('select').formSelect();

  // Initialize tooltips
  $('.tooltipped').tooltip();

  // Initialize dropdowns
  $('.dropdown-trigger').dropdown();
});
