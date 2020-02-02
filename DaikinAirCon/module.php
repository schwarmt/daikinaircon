<?php
	class DaikinAirCon extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

            //Properties
            $this->RegisterPropertyString('IP', 0);
            $this->RegisterPropertyInteger('Period', 15);

            if (!IPS_VariableProfileExists('DaikinAirCon.FanDirection')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanDirection', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanDirection', 0, 2, 1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 0, $this->Translate('Off'), 'Sleep', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 1, $this->Translate('Vertikal-Bewegung'), 'v', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 2, $this->Translate('Horizontal-Bewegung'), 'h', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 3, $this->Translate('beide Bewegungen'), 'both', -1);
            }

            if (!IPS_VariableProfileExists('DaikinAirCon.FanRate')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanRate', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanRate', 0, 2, 1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 0, $this->Translate('Auto'), 'auto', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 1, $this->Translate('Silence'), 'silence', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 2, $this->Translate('Stufe1'), 'level1', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 3, $this->Translate('Stufe2'), 'level2', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 4, $this->Translate('Stufe3'), 'level3', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 5, $this->Translate('Stufe4'), 'level4', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 6, $this->Translate('Stufe5'), 'level5', -1);
            }

            if (!IPS_VariableProfileExists('DaikinAirCon.FanMode')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanMode', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanMode', 0, 2, 1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 2, $this->Translate('Entfeuchten'), 'entf', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 3, $this->Translate('cool'), 'cool', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 4, $this->Translate('heat'), 'heat', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 6, $this->Translate('lueften'), 'lueften', -1);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 7, $this->Translate('auto'), 'auto', -1);
            }


            $this->RegisterVariableInteger('FanDirection', 'FanDirection', 'DaikinAirCon.FanDirection');
            $this->RegisterVariableInteger('FanRate', 'FanRate', 'DaikinAirCon.FanRate');
            $this->RegisterVariableInteger('FanMode', 'FanMode', 'DaikinAirCon.FanMode');
            $this->RegisterVariableBoolean('Power', 'Power', '~Switch');
            $this->RegisterVariableFloat('SollTemperature', 'SollTemperatur', '~Temperature');

            $this->RegisterVariableBoolean('Active', 'Active', '~Switch');
            $this->EnableAction('Active');
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
		}

		public function Send(string $Text)
		{
			//$this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => $Text)));
		}

		public function ReceiveData($JSONString)
		{
			//$data = json_decode($JSONString);
			//IPS_LogMessage("Device RECV", utf8_decode($data->Buffer));
		}

	}