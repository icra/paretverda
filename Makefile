git_push_and_deploy:
	#git: add, commit and push
	git add *
	git commit -am 'update'
	git push

	#desplega al servidor
	# -h  human readable format
	# -P  mostra progrés
	# -vv incrementa verbositat
	# -r  actua recursivament
	rsync -hPvr \
		--exclude ".git" \
		--exclude "deploy.sh" \
		. root@icra.loading.net:/var/www/vhosts/icradev.cat/paretverda.icradev.cat
