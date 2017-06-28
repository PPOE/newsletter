#!/bin/bash
test=`cat mail.txt`
echo "UPDATE presse_content SET content = '${test}' WHERE id = 3" | mysql -u ppoe_mv_ro --password=4fWcBXcbpPuGrjwr ppoe_api_data
