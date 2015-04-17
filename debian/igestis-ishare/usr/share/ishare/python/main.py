#!/usr/bin/env python

import sys, time

sys.path.append('/home/olivierb/Developpements/igestis/igestis-ishare/trunk/usr/share/ishare/python')

if __name__ == "__main__":
	argument = sys.argv
	if sys.argv[1] == "mount_tape":
		print "Tape mounted."
	elif  sys.argv[1] == "umount_tape":
		print "Tape umounted."
