.DEFAULT_GOAL := help

.PHONY: help
help: ## ヘルプ
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
	awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

.PHONY: plantuml
plantuml: ## PlantUML
	docker run -d --rm --name plantuml -p 8080:8080 plantuml/plantuml-server

.PHONY: export-dbdoc
export-dbdoc: ## データーベース定義エクスポート
	docker run --rm -v $(CURDIR):/work -w /work ghcr.io/k1low/tbls doc -c ./doc/.tbls.yml

.PHONY: export-dbdoc-xlsx
export-dbdoc-xlsx: ## データーベース定義書エクスポート
	docker run --rm -v $(CURDIR):/work -w /work ghcr.io/k1low/tbls out -c ./doc/.tbls.yml -t xlsx -o schema.xlsx
