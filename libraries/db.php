<?php

/***
 * POSTGRES
 */
function getLink()
{
        return pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
}

function getResult($link, $sql)
{
	$result = @pg_query($link, $sql);
	if ($result)
	{
		return $result;
	}
	else
	{
		echo "<b>BAD QUERY</b>";
		dump($sql);
	}
}

function fetchResult($result)
{
        return pg_fetch_assoc($result);
}

function startTransaction()
{
        execute("START TRANSACTION");
}

function endTransaction()
{
        execute("COMMIT");
}

/***
 * MYSQL
 *
function getLink()
{
        return pg_connect('host='.SLEDGEMC_HOST.' dbname='.SLEDGEMC_NAME.' user='.SLEDGEMC_USER.' password='.SLEDGEMC_PASS);
}

function getResult($link, $sql)
{
        return pg_query($link, $sql);
}

function fetchResult($result)
{
        return pg_fetch_assoc($result);
}
*/

function stream($sql, $result = null)
{
        $row = true;
        if (!$result)
        {
                $link = getLink();
                $result = getResult($link, $sql);

                return $result;
        }
        if ($result !== true && $result !== false)
        {
                $row = fetchResult($result);
        }

        return $row;
}

function selectOne($sql)
{
        $row = true;

        $link = getLink();
        $result = getResult($link, $sql);

        if ($result !== true && $result !== false)
        {
                $row = fetchResult($result);
        }

        return $row;
}

function selectAll($sqls)
{
        $rows = [];
        if (is_string($sqls))
        {
                $sqls = [$sqls];
        }

        $link = getLink();
        foreach ($sqls as $sql)
        {
                $result = getResult($link, $sql);
                if ($result !== true && $result !== false)
                {
                        while ($row = fetchResult($result))
                        {
                                $rows[] = $row;
                        }
                }
        }

        return $rows;
}

function selectCount($from)
{
        $sql = "SELECT count(*) AS number FROM $from";
        $link = getLink();
        $result = getResult($link, $sql);
        if ($result !== true && $result !== false)
        {
                $row = fetchResult($result);
        }

        return $row['number'];
}

function execute($sqls)
{
        if (is_string($sqls))
        {
                $sqls = [$sqls];
        }
        $link = getLink();
        foreach ($sqls as $sql)
        {
                $result = getResult($link, $sql);
        }
}

function table($results, $class = '')
{
        $html = '';
        if (is_string($results))
        {
                $results = selectAll($results);
        }
        if ($results)
        {
                $html = "<table class='data_table $class'><tr class='alt'>";
                $keys = array_keys($results);
                $first_key = array_shift($keys);
                $columns = count(array_keys($results[$first_key]));
                foreach (array_keys($results[$first_key]) as $value)
                {
                        $html .= "<th>$value</th>";
                }
                $html .= "</tr>";
                foreach ($results as $i => $row)
                {
                        $html .= "<tr".($i % 2 == 0 ? "" : " class='alt'").">";
                        if (count($row) == 1)
                        {
                                $html .= "<td colspan=$columns class='right'>".array_pop($row)."</td>";
                        }
                        else
                        {
                                foreach ($row as $value)
                                {
                                        $html .= "<td>$value</td>";
                                }
                        }
                        $html .= "</tr>";
                }
                $html .= "</table>";
        }

        return $html;
}
