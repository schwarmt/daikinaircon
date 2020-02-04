<?php
	class DaikinAirCon extends IPSModule {

		public function Create()
		{
			//Never delete this line!
			parent::Create();

            //Properties
            $this->RegisterPropertyString('IP', 0);
            $this->RegisterPropertyInteger('Period', 15);

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
            //$this->RegisterVariableFloat('TargetHumidity', $this->Translate('TargetHumidity'), '~Humidity.F');
            $this->RegisterVariableBoolean('Active', $this->Translate('Active'), '~Switch');
            $this->EnableAction('FanDirection');
            $this->EnableAction('FanRate');
            $this->EnableAction('FanMode');
            $this->EnableAction('Power');
            $this->EnableAction('TargetTemperature');
            //$this->EnableAction('TargetHumidity');
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

            if(('' != $this->ReadPropertyString('IP')) && ($this->ReadPropertyInteger('Period') > 0))
            {
                // hier fehlt noch Prüfung
                $this->SetBuffer('StatusBuffer', 'active');
                $this->SetTimerInterval('UpdateData', $this->ReadPropertyInteger('Period') * 1000);
            }
            else{
                // Parameter nicht vollständig
                $this->SetBuffer('StatusBuffer', 'inactive');
                $this->SetTimerInterval('UpdateData', 0);
            }
		}

		public function ActivateUpdates()
        {
            //$this->SetTimerInterval('CheckIfDoneTimer', $this->ReadPropertyInteger('Period') * 1000);
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
        public function SetTargetHumidity(integer $targetHumidity)
        {
            SetValue($this->GetIDForIdent('TargetTemperature'), $targetHumidity);
            $this->SendCommand();
        }

        public function SetActive(bool $Active)
        {
            if ($this->ReadPropertyString('IP') == '') {
                //Modul Deaktivieren
                SetValue($this->GetIDForIdent('Active'), false);
                echo 'No variable selected';
                return false;
            }

            if ($Active) {
                if (GetValue($this->ReadPropertyInteger('SourceID')) >= $this->ReadPropertyFloat('BorderValue')) {
                    SetValue($this->GetIDForIdent('Status'), 1);
                    $this->SetBuffer('StatusBuffer', 'Running');
                } else {
                    SetValue($this->GetIDForIdent('Status'), 0);
                }
            } else {
                SetValue($this->GetIDForIdent('Status'), 0);
            }

            //Modul aktivieren
            SetValue($this->GetIDForIdent('Active'), $Active);
            return true;
        }

        public function SendCommand()
        {
            if($this->GetBuffer('StatusBuffer') == 'active'){
                $ip = $this->ReadPropertyString('IP');
                $url = "http://$ip/aircon/set_control_info";
                $power = GetValueBoolean($this->GetIDForIdent('Power'));
                $mode = GetValueInteger($this->GetIDForIdent('FanMode'));
                $fanspeed = GetValueInteger($this->GetIDForIdent('FanRate'));
                $fandir = GetValueInteger($this->GetIDForIdent('FanDirection'));
                $ttemp = GetValueFloat($this->GetIDForIdent('TargetTemperature'));
                $thum = GetValueFloat($this->GetIDForIdent('TargetHumidity'));

                if ( $power ) {
                    $power = '1';
                } else {
                    $power = '0';
                }

                switch ( $fanspeed ) {
                    case 0:
                        $fanspeed = 'A';
                        break;
                    case 1:
                        $fanspeed = 'B';
                        break;
                    case 2:
                        $fanspeed = '3';
                        break;
                    case 3:
                        $fanspeed = '4';
                        break;
                    case 4:
                        $fanspeed = '5';
                        break;
                    case 5:
                        $fanspeed = '6';
                        break;
                    case 6:
                        $fanspeed = '7';
                        break;
                }
                $data = array('pow' => strval($power), 'mode' => strval($mode), 'stemp' => strval($ttemp), 'shum' => '0', 'f_rate' => strval($fanspeed), 'f_dir' => strval($fandir));
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

        public function UpdateData()
        {
            $ip = $this->ReadPropertyString('IP');

            $result = file_get_contents("http://$ip/aircon/get_control_info");

            //noch zu ergänzen: /aircon/get_sensor_info
            //ret=OK,htemp=23.5,hhum=-,otemp=-,err=0,cmpfreq=0

            echo $result;

            $data = explode(",", $result);
            $values = array();
            foreach ($data as $field)
            {
                preg_match('/([^\=])*\=(.*)/', $data, $matches);
                $values[$matches[1]]=$matches[2];
            }
            print "Ergebnis:";
            print_r($values);
            //echo $data[0]; //ret=       Daten Gültig
            //echo $data[1]; //pow=       Anlage AN
            //echo $data[2]; //mode=      Modus
            //echo $data[3]; //adv=       ?
            //echo $data[4]; //stemp=     Ziel Temperatur
            //echo $data[5]; //shum=      Ziel Feuchte
            //echo $data[6]; //dt1=       Ziel Temp / Mode
            //echo $data[7]; //dt2=       Ziel Temp / Mode
            //echo $data[8]; //dt3=       Ziel Temp / Mode
            //echo $data[9]; //dt4=       Ziel Temp / Mode
            //echo $data[10]; //dt5=      Ziel Temp / Mode
            //echo $data[11]; //dt7=      Ziel Temp / Mode
            //echo $data[12]; //dh1=      Ziel Feuchte / Mode
            //echo $data[13]; //dh2=      Ziel Feuchte / Mode
            //echo $data[14]; //dh3=      Ziel Feuchte / Mode
            //echo $data[15]; //dh4=      Ziel Feuchte / Mode
            //echo $data[16]; //dh5=      Ziel Feuchte / Mode
            //echo $data[17]; //dh7=      Ziel Feuchte / Mode
            //echo $data[18]; //dhh=      Ziel Feuchte / Mode
            //echo $data[19]; //b_mode=
            //echo $data[20]; //b_stemp=
            //echo $data[21]; //b_shum=
            //echo $data[22]; //alert=    Fehlercode
            //echo $data[23]; //f_rate=   Lüfterstufe
            //echo $data[24]; //f_dir=    Lüfter Schwenkmode
            //echo $data[25]; //b_f_rate=
            //echo $data[26]; //b_f_dir=
            //echo $data[27]; //dfr1=
            //echo $data[28]; //dfr2=
            //echo $data[29]; //dfr3=
            //echo $data[30]; //dfr4=
            //echo $data[31]; //dfr5=
            //echo $data[32]; //dfr6=
            //echo $data[33]; //dfr7=
            //echo $data[34]; //dfrh=
            //echo $data[35]; //dfd1=
            //echo $data[36]; //dfd2=
            //echo $data[37]; //dfd3=
            //echo $data[38]; //dfd4=
            //echo $data[39]; //dfd5=
            //echo $data[40]; //dfd6=
            //echo $data[41]; //dfd7=
            //echo $data[42]; //dfdh=


            if ( $data[0] == "ret=OK" ) {
                //echo "Daten Gültig!" ;
                $power=substr($data[1],4,1);
                SetValue($this->GetIDForIdent('Power'), intval($power));

                $mode=substr($data[2],5,1);
                SetValue($this->GetIDForIdent('FanMode'), intval($mode));

                $stemp=substr($data[4],6,4);
                if ( $stemp != "--" ) {
                    SetValue($this->GetIDForIdent('TargetTemperature'), floatval($stemp));
                }

                $shum=substr($data[5],6,4);
                SetValue($this->GetIDForIdent('TargetHumidity'), floatval($shum));

                $frate=substr($data[23],7,1);
                switch ($frate) {
                    case 'A':
                        $stufe=0;
                        break;
                    case 'B':
                        $stufe=1;
                        break;
                    case '3':
                        $stufe=2;
                        break;
                    case '4':
                        $stufe=3;
                        break;
                    case '5':
                        $stufe=4;
                        break;
                    case '6':
                        $stufe=5;
                        break;
                    case '7':
                        $stufe=6;
                        break;
                }
                SetValue($this->GetIDForIdent('FanRate'), $stufe);

                $fdir=substr($data[24],6,1);
                SetValue($this->GetIDForIdent('FanDirection'), intval($fdir));


            } else {

                echo "Daten ungültig!";
            }

        }

        public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
        {
            IPS_LogMessage("MessageSink", "Message from SenderID ".$SenderID." with Message ".$Message."\r\n Data: ".print_r($Data, true));
           // if (GetValue($this->GetIDForIdent('Active'))) {
           //     if (($Data[0] < $this->ReadPropertyFloat('BorderValue')) && (GetValue($this->GetIDForIdent('Status')) == 1) && ($this->GetBuffer('StatusBuffer') == 'Running')) {
           //         $this->SetTimerInterval('CheckIfDoneTimer', $this->ReadPropertyInteger('Period') * 1000);
           //         $this->SetBuffer('StatusBuffer', 'Done');
           //     } elseif ($Data[0] > $this->ReadPropertyFloat('BorderValue')) {
           //         SetValue($this->GetIDForIdent('Status'), 1);
           //         $this->SetTimerInterval('CheckIfDoneTimer', 0);
           //         $this->SetBuffer('StatusBuffer', 'Running');
           //     }
          //  }
        }


	}