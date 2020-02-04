<?php
	class DaikinAirCon extends IPSModule {

	    const STATUS_ACTIVE = 102;
	    const STATUS_INACTIVE = 104;
	    const STATUS_ERROR = 200;


		public function Create()
		{
			//Never delete this line!
			parent::Create();

            //Properties
            $this->RegisterPropertyString('IP', 0);
            $this->RegisterPropertyInteger('Period', 120);

            //Timer
            $this->RegisterTimer('UpdateData', 0, 'DAC_UpdateData($_IPS[\'TARGET\']);');

            // Variable Profiles
            if (!IPS_VariableProfileExists('DaikinAirCon.FanDirection')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanDirection', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanDirection', 0, 0, 0);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 0, $this->Translate('off'), '', 65280);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 1, $this->Translate('swingVertical'), '', 16776960);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 2, $this->Translate('swingHorizontal'), '', 16744448);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanDirection', 3, $this->Translate('swingBoth'), '', 16711680);
            }

            if (!IPS_VariableProfileExists('DaikinAirCon.FanRate')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanRate', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanRate', 0, 0, 0);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 0, $this->Translate('auto'), '', 16776960);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 1, $this->Translate('indoorUnitQuiet'), '', 65280);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 2, $this->Translate('low'), '', 16744576);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 3, $this->Translate('middleLow'), '', 16744512);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 4, $this->Translate('middle'), '', 16744448);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 5, $this->Translate('middleHigh'), '', 8404992);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanRate', 6, $this->Translate('high'), '', 8388608);
            }

            if (!IPS_VariableProfileExists('DaikinAirCon.FanMode')) {
                IPS_CreateVariableProfile('DaikinAirCon.FanMode', 1);
                IPS_SetVariableProfileValues('DaikinAirCon.FanMode', 0, 0, 0);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 2, $this->Translate('dry'), 'Drops', 32896);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 3, $this->Translate('cool'), 'Snowflake', 65535);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 4, $this->Translate('heat'), 'Flame', 16711680);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 6, $this->Translate('fan'), 'Shuffle', 16777088);
                IPS_SetVariableProfileAssociation('DaikinAirCon.FanMode', 7, $this->Translate('auto'), 'Gear', 255);
            }

            $this->RegisterVariableInteger('FanDirection', $this->Translate('FanDirection'), 'DaikinAirCon.FanDirection');
            $this->RegisterVariableInteger('FanRate', $this->Translate('FanRate'), 'DaikinAirCon.FanRate');
            $this->RegisterVariableInteger('FanMode', $this->Translate('FanMode'), 'DaikinAirCon.FanMode');
            $this->RegisterVariableBoolean('Power', $this->Translate('Power'), '~Switch');
            $this->RegisterVariableFloat('TargetTemperature', $this->Translate('TargetTemperature'), '~Temperature.Room');
            $this->RegisterVariableFloat('InsideTemperature', $this->Translate('InsideTemperature'), '~Temperature.Room');
            $this->RegisterVariableBoolean('Active', $this->Translate('Active'), '~Switch');
            $this->EnableAction('FanDirection');
            $this->EnableAction('FanRate');
            $this->EnableAction('FanMode');
            $this->EnableAction('Power');
            $this->EnableAction('TargetTemperature');
            $this->EnableAction('Active');
            $this->SetStatus(self::STATUS_INACTIVE);
            //$this->SetBuffer('StatusBuffer', 'inactive');
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		private function UpdateStatus(string $status)

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();
            $this->validateConnectionAndStatus(boolval(GetValueBoolean($this->GetIDForIdent('Active'))));
		}

        public function RequestAction($Ident, $Value)
        {
            echo "RequestAction";
            switch ($Ident) {
                case 'Power':
                case 'FanDirection':
                case 'FanRate':
                case 'FanMode':
                case 'TargetTemperature':
                    SetValue($this->GetIDForIdent($Ident), $Value);
                    echo "Request Action started\n";
                    $this->SendCommand();
                    break;
                case 'Active':
                    $this->SetActive($Value);
                    break;
                default:
                    throw new Exception('Invalid Ident');
            }
        }

        public function SetFanDirection(integer $fanDirection)
        {
            SetValue($this->GetIDForIdent('FanDirection'), $fanDirection);
            $this->SendCommand();
        }
        public function SetFanRate(integer $fanRate)
        {
            SetValue($this->GetIDForIdent('FanRate'), $fanRate);
            $this->SendCommand();
        }
        public function SetFanMode(integer $fanMode)
        {
            SetValue($this->GetIDForIdent('FanMode'), $fanMode);
            $this->SendCommand();
        }
        public function SetPower(bool $power)
        {
            SetValue($this->GetIDForIdent('Power'), $power);
            $this->SendCommand();
        }
        public function SetTargetTemperature(float $targetTemperature)
        {
            SetValue($this->GetIDForIdent('TargetTemperature'), $targetTemperature);
            $this->SendCommand();
        }

        public function SetActive(bool $Active)
        {
            SetValue($this->GetIDForIdent('Active'), $Active);
            $this->validateConnectionAndStatus($Active);
            return true;
        }

        public function CheckConnection()
        {
            $result = $this->validateConnectionAndStatus(true);
            if($result == true){
                SetValue($this->GetIDForIdent('Active'), true);
            }

        }

        private function validateConnectionAndStatus(bool $active)
        {
            $success=false;
            if (($this->ReadPropertyString('IP') != "") && ($this->ReadPropertyInteger('Period') > 0)) {
                if ($active) {
                    $success = $this->UpdateData();
                    if ($success == true) {
                        $this->UpdateStatus(self::STATUS_ACTIVE);
                        //$this->SetBuffer('StatusBuffer', 'active');
                        $this->SetTimerInterval('UpdateData', $this->ReadPropertyInteger('Period') * 1000);
                    } else {
                        $this->UpdateStatus(self::STATUS_ERROR);
                        //$this->SetBuffer('StatusBuffer', 'error');
                        $this->SetTimerInterval('UpdateData', 0);
                    }

                } else {
                    $this->UpdateStatus(self::STATUS_INACTIVE);
                   // $this->SetBuffer('StatusBuffer', 'inactive');
                    $this->SetTimerInterval('UpdateData', 0);
                }
            } else {
                // Parameter nicht vollständig
                $this->UpdateStatus(self::STATUS_INACTIVE);
                //$this->SetBuffer('StatusBuffer', 'inactive');
                $this->SetTimerInterval('UpdateData', 0);
            }
            return $success;
        }

        public function SendCommand()
        {
            if($this->GetStatus() == self::STATUS_ACTIVE){
//            if($this->GetBuffer('StatusBuffer') == 'active'){
                $ip = $this->ReadPropertyString('IP');
                $url = "http://$ip/aircon/set_control_info";
                $fanRatesRev = array(
                    0 => "A", 1 => "B", 2 => "3", 3 => "4", 4 => "5", 5 => "6", 6 => "7");
                $data = array(
                    'pow' => strval(GetValueBoolean($this->GetIDForIdent('Power')) ? "1" : "0"),
                    'mode' => strval(GetValueInteger($this->GetIDForIdent('FanMode'))),
                    'stemp' => strval(GetValueFloat($this->GetIDForIdent('TargetTemperature'))),
                    'shum' => '0',
                    'f_rate' => strval($fanRatesRev[GetValueInteger($this->GetIDForIdent('FanRate'))]),
                    'f_dir' => strval(GetValueInteger($this->GetIDForIdent('FanDirection'))));
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                        'method'  => 'GET',
                        'content' => http_build_query($data)
                    )
                );
                $content = http_build_query($data);
                $context  = stream_context_create($options);
                file_get_contents("$url?$content", false, $context);
            }
        }

        private function readDaikinACStatus(string $api): array
        {
            $ip = $this->ReadPropertyString('IP');
            $result = file_get_contents("http://$ip/aircon/$api");
            $data = explode(",", $result);
            $values = array();
            foreach ($data as $field)
            {
                preg_match('/([^=])*=(.*)/', $field, $matches);
                $values[$matches[1]]=$matches[2];
            }
            print "Ergebnis:";
            print_r($values);
            return $values;
        }

        public function UpdateData()
        {
            $values = $this->readDaikinACStatus("get_sensor_info");
            if($values["ret"]=="OK")
            {
                SetValue($this->GetIDForIdent('InsideTemperature'), floatval($values["htemp"]));
            };

            $values = $this->readDaikinACStatus("get_control_info");
            if($values["ret"]=="OK")
            {
                SetValue($this->GetIDForIdent('Power'), intval($values["pow"]));
                SetValue($this->GetIDForIdent('FanMode'), intval($values["mode"]));
                if($values["stemp"] != "--")
                {
                    SetValue($this->GetIDForIdent('TargetTemperature'), floatval($values["stemp"]));
                }
                $fanRates = array(
                    "A" => 0, "B" => 1, "3" => 2, "4" => 3, "5" => 4, "6" => 5, "7" => 6);
                SetValue($this->GetIDForIdent('FanRate'), $fanRates[$values["f_rate"]]);
                SetValue($this->GetIDForIdent('FanDirection'), intval($values["f_dir"]));
                return true;
            } else {
                return false;
            }
        }
	}