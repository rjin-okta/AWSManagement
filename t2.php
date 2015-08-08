<?php
	require ('vendor/autoload.php');
#	use Aws\Common\Aws;

	$tag='architecture';
	$values=array('i386','x86_64');

	$client = new Aws\Ec2\Ec2Client(array(
		'region' => 'us-west-2',
		'debug' => false,
		'version' => 'latest',
		'profile' => 'default'
	));

	$owner = "B.Gates";

	$param=array(
		'Filters'=>array(
			array('Name' => $tag,'Values' => $values),
			array('Name' => 'tag:Name','Values' => array('EU *')),
			array('Name' => 'tag:Owners','Values' => array($owner,$owner . ",*","*,".$owner.",*","*," . $owner)),
		)
	);

	$result = $client->describeInstances($param);
	
	foreach ($result['Reservations'] as $reservation) {
		foreach ($reservation['Instances'] as $instance) {
			foreach ($instance['Tags'] as $instanceTag) {
				$instanceName = '';
				if ($instanceTag['Key'] == 'Name') {
					$instanceName = $instanceTag['Value'];
				}
			}
			
			print "Instance Name: " . $instanceName . "\n";
			print "Instance ID: ". $instance['InstanceId'] . "\n";
			print "----> State: " . $instance['State']['Name'] . "\n";
			print "----> Instance Type: " . $instance['InstanceType'] . "\n";

			foreach ($instance['NetworkInterfaces'] as $networkinterface) {
				print "----> Network InterfaceId: " . $networkinterface['NetworkInterfaceId'] . "\n";
				print "----> Network Interface MAC Address: " . $networkinterface['macAddress'] . "\n";
				$num_ipaddress=sizeof($networkinterface['PrivateIpAddresses']) . "\n";
				for ($i=0;$i<$num_ipaddress;$i++) {
					if ($i == 0) {
						print "----> Network Interface Primary Private Ip Address: ";
					} else {
						print "----> Instance Other Private Address: ";
					}
					print $networkinterface['PrivateIpAddresses'][$i]['PrivateIpAddress'] . "\n";
				}
			};
		}
	}
	
?>
