<?php
class Funcsapi
{
	private $dirHost;
	
	public function __construct()
	{
		$this->dirHost = realpath(dirname(__FILE__) . '/');
	}
	
	public function getNews($newsId,$params)
	{
		$conn = new mysqli($params["host"], $params["username"], $params["password"],$params["dbname"]);
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->query("SET NAMES utf8");
		
		$sql = "SELECT ID,post_date,post_content,post_title FROM wp_posts
		WHERE post_status='publish' AND post_type='post' AND ID='".$newsId."'";
		
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				$data = $row;
				$date = explode(' ', $row["post_date"]);
				$data["post_date"] = $this->mitosh($date[0]).' '.$date[1];
				
				$data["post_content"] = str_replace("<img ", '<img width="100%" ', $data["post_content"]);
			}
			
			$sql = "SELECT post_parent,guid FROM wp_posts
			WHERE post_parent='".$newsId."' AND post_type='attachment' AND post_status='inherit'
			GROUP BY post_parent";
			$result1 = $conn->query($sql);
			if ($result1->num_rows > 0)
			{
				while($row1 = $result1->fetch_assoc())
				{
					$data["post_image"] = $row1["guid"];
				}
			}
		}
		else
			$data = null;
		
		$conn->close();
		return $data;
	}
	
	public function getPosts($params,$token=0)
	{
		$conn = new mysqli($params["host"], $params["username"], $params["password"],$params["dbname"]);
		
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$conn->query("SET NAMES utf8");
		
		/* $readMaxId = $this->getTxt($token);
		//echo $readMaxId;exit;
		if(empty($readMaxId)) 
			$readMaxId = 0; */
		
		/* $sql = "SELECT * FROM apinews
			WHERE token='".$token."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				$readMaxId = $row["lastId"];
			}
		}
		else  */
			$readMaxId = 0;
		
		$sql = "SELECT ID,post_date,post_content,post_title FROM wp_posts
		WHERE post_status='publish' AND post_type='post' AND ID>'".$readMaxId."' AND post_date > NOW() - INTERVAL 72 HOUR
		ORDER BY post_date DESC";
		
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
			{
				$content1 = null;
				$date = null;
		
				$data["items"][$row["ID"]] = $row;
		
				$content = $row["post_content"];
				$content = strip_tags(trim($row["post_content"]));
				$content1 = explode(' ', $content);
				$content2 = '';
				$m = 0;
				while ($m<=20)
				{
					$content2 .= $content1[$m].' ';
					$m++;
				}
				$content2 .= '...';

				$content2 = str_replace("\n", "", $content2);
				$content2 = str_replace("\r", "", $content2);
				
				$data["items"][$row["ID"]]["post_content"] = $content2;
		
				$date = explode(' ', $row["post_date"]);
				$data["items"][$row["ID"]]["post_date"] = $this->mitosh($date[0]).' '.$date[1];
				
				$data["items"][$row["ID"]]["post_link"] = MYURL.'apinews/content.php?i='.$row["ID"];
		
				$selarr .= $row["ID"].',';
				$fileArr[] = $row["ID"];
			}
		
			$selarr = substr($selarr, 0,-1);
			$sql = "SELECT post_parent,guid FROM wp_posts 
			WHERE post_parent IN (".$selarr.") AND post_type='attachment' AND post_status='inherit' 
			GROUP BY post_parent";
			$result = $conn->query($sql);
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$data["items"][$row["post_parent"]]["post_image"] = $row["guid"];
				}
			}
		
			$sql = "SELECT wp_terms.`name`,wp_term_relationships.object_id FROM wp_term_relationships
			Inner Join wp_term_taxonomy ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
			Inner Join wp_terms ON wp_terms.term_id = wp_term_taxonomy.term_id
			WHERE
			wp_term_taxonomy.taxonomy = 'category' AND wp_term_relationships.object_id IN (".$selarr.")";
			$result = $conn->query($sql);
			
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
				{
					$data["items"][$row["object_id"]]["post_category"] = $row["name"];
				}
			}
			
			$maxId = max($fileArr);
			//$this->saveTxt($token, $maxId);
			/* if(!empty($readMaxId))
			{
				$sql = "UPDATE apinews SET lastId='".$maxId."' WHERE token='".$token."'";
				$result = $conn->query($sql);
			}
			else
			{
				$sql = "INSERT INTO apinews(token,lastId) VALUES('".$token."','".$maxId."')";
				$result = $conn->query($sql);
			} */
			
			
			$data1["ok"] = true;
			
			$i =0;
			foreach($data["items"] as $myitem)
			{
				$data1["items"][$i] = $myitem;
				$i++;
			}
		}
		else
		{
			$data1["ok"] = false;
		}		
		
		$conn->close();
		//$jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
		//return $jsonData;
		return $data1;
	}
	
	public function mitosh($TheDate)
	{
		$grgYear = substr($TheDate,0,4);
		$grgMonth = substr($TheDate,5,2);
		$grgDay = substr($TheDate,8,2);
		$grgSumOfDays=array(array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365),array(0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366));
		$hshSumOfDays=array(array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 365), array(0, 31, 62, 93, 124, 155, 186, 216, 246, 276, 306, 336, 366));
		$hshYear = $grgYear-621;
		
		$grgLeap=$this->grgIsLeap ($grgYear);
		$hshLeap=$this->hshIsLeap ($hshYear-1);
		
		$grgElapsed = $grgSumOfDays[($grgLeap ? 1:0)][$grgMonth-1]+$grgDay;
		
		$XmasToNorooz = ($hshLeap && $grgLeap) ? 80 : 79;
		
		if ($grgElapsed <= $XmasToNorooz)
		{
			$hshElapsed = $grgElapsed+286;
			$hshYear--;
			if ($hshLeap && !$grgLeap)
				$hshElapsed++;
		}
		else
		{
			$hshElapsed = $grgElapsed - $XmasToNorooz;
			$hshLeap = $this->hshIsLeap ($hshYear);
		}
		
		for ($i=1; $i <= 12 ; $i++)
		{
			if ($hshSumOfDays [($hshLeap ? 1:0)][$i] >= $hshElapsed)
			{
				$hshMonth = $i;
				$hshDay = $hshElapsed - $hshSumOfDays [($hshLeap ? 1:0)][$i-1];
				break;
			}
		}
		
		if ($hshMonth < 10)
		{
			$hshMonth ='0'.$hshMonth;
		}
		if ($hshDay < 10)
		{
			$hshDay ='0'.$hshDay;
		}
		return $hshYear . "/" . $hshMonth . "/" . $hshDay;
	}
	
	public function grgIsLeap ($Year)
	{
		return (($Year%4) == 0 && (($Year%100) != 0 || ($Year%400) == 0));
	}
		
	public function hshIsLeap ($Year)
	{
		$Year = ($Year - 474) % 128;
		$Year = (($Year >= 30) ? 0 : 29) + $Year;
		$Year = $Year - floor($Year/33) - 1;
		return (($Year % 4) == 0);
	}
	
	/* public function saveTxt($strId)
	{				
		$filename = $this->dirHost.'/text.txt';
		
		$handle = fopen($filename, 'w');
		
		if (fwrite($handle, $strId) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}
		
		fclose($handle);
	}
	
	public function getTxt()
	{
		$filename = $this->dirHost.'/text.txt';
	
		$handle = fopen($filename, 'r');
		
		$contents = fread($handle, filesize($filename));
		
		fclose($handle);
		
		return $contents;
	} */
	
	/* public function saveTxt($token,$lastId)
	{

		$xml = simplexml_load_file('info.xml');
		
		if(empty($xml->$token))
		{
			$xml->addChild($token, $lastId);
		}
		else 
		{
			$xml->$token = $lastId;
		}
		file_put_contents('info.xml', $xml->asXML());
		
		return true;
	}
	
	public function getTxt($token)
	{
		$doc = new DOMDocument();
		$doc->load(MYURL.'info.xml');
		
		if(!empty($doc->getElementsByTagName('mgh')))
		{
			foreach($doc->getElementsByTagName('mgh') as $node)
			{
				if(!empty($node->getElementsByTagName($token)->item(0)->nodeValue))
				{
					$res =  $node->getElementsByTagName($token)->item(0)->nodeValue;
				}
				else
					$res = 0;
			}
		}
		else
			$res = 0;
		
		return $res;
	}  */
}