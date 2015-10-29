<?php
/**
*
* NOTE
* You are free edit and play around with the module.
*
*  @author    oldlastman
*  @copyright attendants
*  @version   0.1.1
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
if ( !defined( '_PS_VERSION_' ) )
	exit;
class attredirector extends Module
{
	public function __construct()
	{
		$this->name = 'attredirector';
		$this->tab = 'front_office_features';
		$this->version = 0.1.1;
		$this->author = 'oldlastman Attendants';
		$this->need_instance = 0;
		$this->module_key = "";
		parent::__construct();
		$this->displayName = $this->l( 'Attendants redirector' );
		$this->description = $this->l( 'Redirecciona a tienda concreta si segmento de url coincide con almacenado.' );
		$this->confirmUninstall = $this->l('Are you sure you want to delete attredirector ?');
	}
	public function install()
	{
		if (!parent::install() OR !$this->registerHook('displayHeader'))
			return false;
		return true;
	}
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		return true;
	}
	public function hookDisplayHeader( $params )
	{
		$tiendaActual = Configuration::get('att_tiendaLlegada');
    
		$array_gruposPermitidos = explode(',', Configuration::get('att_terminosAceptados'));

		$urlSegment = explode('/', $_SERVER['REQUEST_URI']);
		
		if(in_array($urlSegment[1], $array_gruposPermitidos)){
			//redirigir a			
			header('Location: '. __PS_BASE_URI__ .$tiendaActual,TRUE,301);
		}

		return ;
	}
	public function getContent()
	{
		if (Tools::isSubmit('submit'.$this->name))
	    {
	        //att_tiendaLlegada
	        $att_tiendaLlegada = strval(Tools::getValue('att_tiendaLlegada'));
	        if (!$att_tiendaLlegada
	          || empty($att_tiendaLlegada)
	          || !Validate::isGenericName($att_tiendaLlegada))
	            $output .= $this->displayError($this->l('Invalid Configuration tiendaLlegada'));
	        else
	        {
	            Configuration::updateValue('att_tiendaLlegada', $att_tiendaLlegada);
	            //$output .= $this->displayConfirmation($this->l('Settings updated'));
	        }	        
	        //att_terminosAceptados
	        $att_terminosAceptados = strval(Tools::getValue('att_terminosAceptados'));
	        if (!$att_terminosAceptados
	          || empty($att_terminosAceptados)
	          || !Validate::isGenericName($att_terminosAceptados))
	            $output .= $this->displayError($this->l('Invalid Configuration terminosAceptados'));
	        else
	        {
	            Configuration::updateValue('att_terminosAceptados', $att_terminosAceptados);
	           // $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }


		$output = '<h2>'.$this->displayName.'</h2>';
		$output .= '<p>Redirecciona a tienda concreta si segmento de url coincide con almacenado.</p>';

		$output .= $this->displayForm();
		
		return $output;
	}

	private function displayForm(){
    // Get default language
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
     
    // Init Fields form array
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Settings'),
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Tienda a la que redirigir'),
                'name' => 'att_tiendaLlegada',
                'size' => 50,
                'required' => true
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Listado terminos aceptados'),
                'name' => 'att_terminosAceptados',
                'size' => 50,
                'required' => true
            )
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'button'
        )
    );
     
    $helper = new HelperForm();
     
    // Module, token and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
     
    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
     
    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );
     
    // Load current value
    $helper->fields_value['att_tiendaLlegada'] = Configuration::get('att_tiendaLlegada');
    $helper->fields_value['att_terminosAceptados'] = Configuration::get('att_terminosAceptados');
     
    return $helper->generateForm($fields_form);
}
}
?>