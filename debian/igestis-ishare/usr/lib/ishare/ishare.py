
import os, sys, random, subprocess, parted

# Emplacement du montage de la cassette

supported_drives = ['RDX', 'RD1000', 'GoVault']
verbose = "no"

class Tape:

	def __init__(self):

		devices = os.listdir('/sys/block/')
		for dev in devices:
			try:
				f = open('/sys/block/' + dev + '/device/model', 'r')
				t = f.read()
				for drives in supported_drives:
					if drives in t:
						self.dev = dev
						break
				if self.dev:
					break
			except:
				self.dev = None


	def mount(self):
		if not self.dev:
			if verbose == "yes":
				print "There is no compatible backup drive on this system"
			print "There is no compatible backup drive on this system"
			return 1
		r = str(random.randint(100,999))
		mount_path='/tmp/tape-'+r
		while os.path.exists(mount_path):
			r = random.randint(100,999)
			mount_path='/mnt/tape-',r
		if not os.path.exists('/dev/' + self.dev + '1'):
			print "The tape is not partitionned correctly"
			return 2
		try:
			os.mkdir(mount_path)
			m = subprocess.call('mount '+ self.dev + '1 ' + mount_path)
		except:
			if verbose == "yes":
				print "An error has occured while mounting", self.dev + '1'
		else:
			print "Tape mounted on", mount_path
			return mount_path


	def mount_path(self):
		if not self.dev:
			if verbose == "yes":
				print "There is no compatible backup drive on this system"
			return 1
		try:
			with open("/proc/mounts", "r") as ifp:
				for line in ifp:
					fields= line.rstrip('\n').split()
					if fields[0] == '/dev/' + self.dev + '1':
						return fields[1]
		except EnvironmentError:
			pass
		else:
				return 0

	def umount(self):
		if not self.dev:
			if verbose == "yes":
				print "There is no compatible backup drive on this system"
			return 1
		while self.mount_path():
			try:
				subprocess.call('umount '+ self.dev + '1')
			except:
				print "An error has occured while umounting", self.dev + '1'
				return 1
			else:
				return 0

	def mkfs(self):
		if not self.dev:
			if verbose == "yes":
				print "There is no compatible backup drive on this system"
			return 1
		try:
			command='parted ' + self.dev + '1' + ' mklabel msdos -s'
			subprocess.call(command.split(), shell=False)
			m = subprocess.call('parted ' + self.dev + "1" + ' unit GB print | grep $tape  | awk \'{print $3}\'')
			subprocess.call('parted ' + self.dev + "1" + ' mkpartfs primary ext2 1049kB $size -s')
		except:
			if verbose == "yes":
				print "Unable to format the drive", self.dev + '1'















