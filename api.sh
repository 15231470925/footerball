#!/bin/bash
# WordPress REST API 快捷操作脚本
# 使用方法: ./api.sh [command] [options]

BASE_URL="https://football.dhgate.com"
AUTH_HEADER="Authorization: Basic bGl1eGl5dW5AZGhnYXRlLmNvbTpUckZmIG13NzEgY0p0ayBsVXB0IFNCa1UgQlNHSA=="

# 颜色输出
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

usage() {
    echo -e "${GREEN}WordPress REST API 快捷操作脚本${NC}"
    echo ""
    echo "用法: ./api.sh <command> [options]"
    echo ""
    echo "命令："
    echo "  settings                    获取站点设置"
    echo "  pages                       获取所有页面"
    echo "  posts                       获取所有文章"
    echo "  players                     获取所有球员"
    echo "  teams                       获取所有球队"
    echo "  schedules                   获取所有赛程"
    echo "  lifestyle                   获取所有生活内容"
    echo "  media [count]               获取媒体文件（默认5个）"
    echo ""
    echo "  get-page <id>               获取指定页面"
    echo "  get-player <id>             获取指定球员"
    echo "  get-team <id>               获取指定球队"
    echo ""
    echo "  create-post <title> <content>  创建新文章"
    echo "  create-player <name>        创建新球员"
    echo ""
    echo "  update-page <id> <content>  更新页面内容"
    echo "  delete-post <id>            删除文章"
    echo ""
    echo "  player-detail <id>          查看球员详情"
    echo "  acf-fields                  查看 ACF 字段结构"
}

case "$1" in
    settings)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/settings" | python3 -m json.tool
        ;;
    pages)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/pages?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); [print(f'ID:{p[\"id\"]} | {p[\"title\"][\"rendered\"]} | slug:{p[\"slug\"]}') for p in data]"
        ;;
    posts)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/posts?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); print(f'共 {len(data)} 篇文章'); [print(f'ID:{p[\"id\"]} | {p[\"title\"][\"rendered\"]}') for p in data]"
        ;;
    players)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/players?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); print(f'共 {len(data)} 名球员'); [print(f'ID:{p[\"id\"]} | {p[\"title\"][\"rendered\"]}') for p in data]"
        ;;
    teams)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/teams?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); print(f'共 {len(data)} 支球队'); [print(f'ID:{p[\"id\"]} | {p[\"title\"][\"rendered\"]}') for p in data]"
        ;;
    schedules)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/schedules?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); print(f'共 {len(data)} 条赛程')"
        ;;
    lifestyle)
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/lifestyle?per_page=100" | \
            python3 -c "import sys,json; data=json.load(sys.stdin); print(f'共 {len(data)} 条生活内容'); [print(f'ID:{p[\"id\"]} | {p[\"title\"][\"rendered\"]}') for p in data]"
        ;;
    media)
        COUNT=${2:-5}
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/media?per_page=$COUNT" | \
            python3 -c "
import sys,json
data=json.load(sys.stdin)
for item in data:
    title = item.get('title',{}).get('rendered','N/A')
    mime = item.get('mime_type','N/A')
    src = item.get('source_url','N/A')
    print(f'ID:{item[\"id\"]} | {title} | {mime} | {src}')
"
        ;;
    get-page)
        if [ -z "$2" ]; then echo "请提供页面 ID"; exit 1; fi
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/pages/$2" | python3 -m json.tool
        ;;
    get-player)
        if [ -z "$2" ]; then echo "请提供球员 ID"; exit 1; fi
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/players/$2" | python3 -m json.tool
        ;;
    get-team)
        if [ -z "$2" ]; then echo "请提供球队 ID"; exit 1; fi
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/teams/$2" | python3 -m json.tool
        ;;
    player-detail)
        if [ -z "$2" ]; then echo "请提供球员 ID"; exit 1; fi
        echo -e "${YELLOW}=== 球员详情 ===${NC}"
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/players/$2" | python3 -c "
import sys, json
data = json.load(sys.stdin)
print(f'名称: {data[\"title\"][\"rendered\"]}')
print(f'ID: {data[\"id\"]}')
print(f'Slug: {data[\"slug\"]}')
print(f'链接: {data[\"link\"]}')
if data.get('meta'):
    print('\\n=== 自定义字段 (ACF) ===')
    for k, v in data['meta'].items():
        if v: print(f'  {k}: {v}')
"
        ;;
    create-post)
        if [ -z "$2" ] || [ -z "$3" ]; then echo "用法: ./api.sh create-post <标题> <内容>"; exit 1; fi
        curl -s -X POST \
            -H "$AUTH_HEADER" \
            -H "Content-Type: application/json" \
            -d "{\"title\":\"$2\",\"content\":\"$3\",\"status\":\"publish\"}" \
            "$BASE_URL/wp-json/wp/v2/posts" | python3 -m json.tool
        ;;
    update-page)
        if [ -z "$2" ] || [ -z "$3" ]; then echo "用法: ./api.sh update-page <ID> <内容>"; exit 1; fi
        curl -s -X POST \
            -H "$AUTH_HEADER" \
            -H "Content-Type: application/json" \
            -d "{\"content\":\"$3\"}" \
            "$BASE_URL/wp-json/wp/v2/pages/$2" | python3 -m json.tool
        ;;
    delete-post)
        if [ -z "$2" ]; then echo "请提供文章 ID"; exit 1; fi
        curl -s -X DELETE -H "$AUTH_HEADER" "$BASE_URL/wp-json/wp/v2/posts/$2?force=true" | python3 -m json.tool
        ;;
    acf-fields)
        echo -e "${YELLOW}=== ACF 选项页面字段 ===${NC}"
        curl -s -H "$AUTH_HEADER" "$BASE_URL/wp-json/acf/v3/options/home-settings" 2>/dev/null | python3 -m json.tool 2>/dev/null || \
            echo "需要安装 ACF to REST API 插件才能查看 ACF 字段"
        ;;
    *)
        usage
        ;;
esac
