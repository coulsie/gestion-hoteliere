// Call the dataTables jQuery plugin
$(document).ready(function() {
  
  $('#tableMembres').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.3.3/i18n/fr-FR.json'
                    }
                });

  $('#tableCotisations').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.3.3/i18n/fr-FR.json'
                    }
                });

                
  $('#tableCotisationsExceptionnelles').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.3.3/i18n/fr-FR.json'
                    }
                });


  $('#tablePaiements').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/2.3.3/i18n/fr-FR.json'
                    }
                });
});

