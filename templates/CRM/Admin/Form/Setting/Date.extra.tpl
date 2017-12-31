<div class="to_move_fieldset" style="display:none">
  <fieldset>
    <legend>{ts}Export Date Fields Format{/ts}</legend>
    <table class="exportDate form-layout-compressed">
      <tr class="crm-date-form-block-exportFormatDate">
        <td class="label">{$form.exportFormatDate.label}</td>
        <td>{$form.exportFormatDate.html}</td>
      </tr>
      <tr class="crm-date-form-block-exportFormatDateTime">
        <td class="label">{$form.exportFormatDateTime.label}</td>
        <td>{$form.exportFormatDateTime.html}</td>
      </tr>
    </table>
  </fieldset>
</div>
{literal}
<script type="text/javascript">
CRM.$(function($) {
  $(".crm-date-form-block > fieldset:nth-child(3)").after($('div.to_move_fieldset fieldset'));
});
</script>
{/literal}
