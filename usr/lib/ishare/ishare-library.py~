#!/bin/sh

import os, sys

# Emplacement du montage de la cassette


class Tape:
	tape_tmp_mount = '/mnt/tape'

	def __init__(self):
		devices = os.listdir('/sys/block/')
		for file in devices:
			try:
				f = open(devices, 'w')
				device_name = f.readline()
				if device_name == "RDX":
					device_dev = devices
					break
			except ValueError:
				print "There is no compatible backup drive on this system !"

