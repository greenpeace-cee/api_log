<div class="api-log__settings-wrap">
  <div class="crm-block crm-form-block">
      {*ADD, UPDATE*}
      {if $action == 1 or $action == 2}
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
        <div class="crm-submit-buttons">
            {include file="CRM/common/formButtons.tpl" location="bottom"}
        </div>
      {/if}

      {*DELETE*}
      {if $action == 8}
        <h3>{ts domain=api_log 1=$apiConfig.title}Delete Config "%1"{/ts}</h3>
        <div class="messages status">
          <p>{ts domain=api_log}Are you really want to delete?{/ts}</p>
        </div>
        <div class="api-log__settings-buttons-wrap crm-submit-buttons">
            {include file="CRM/common/formButtons.tpl" location="bottom"}
        </div>
      {/if}
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
