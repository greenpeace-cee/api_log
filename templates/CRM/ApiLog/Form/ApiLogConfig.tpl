<div class="api-log__settings-wrap">
  <div class="crm-block crm-form-block">

    <div class="api-log__settings">
      <div class="api-log__settings-items">
          {foreach from=$settingsNames item=elementName}
            <div class="crm-section">
              <div class="label">{$form.$elementName.label}</div>
              <div class="content">{$form.$elementName.html}</div>
              <div class="clear"></div>
            </div>
          {/foreach}
      </div>
    </div>

    <div class="api-log__settings-buttons-wrap crm-submit-buttons">
        {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  </div>
</div>

{literal}
  <style>
      .api-log__settings-buttons-wrap {
          display: flex;
          padding: 10px;
      }

      .api-log__settings-items {
          padding: 20px 10px;
      }
  </style>
{/literal}
