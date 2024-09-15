ros_version=7.15.3
get_interface_name=$(VBoxManage list bridgedifs | awk 'NR==1 {print $2; exit}')

if ! test -f chr-$ros_version.vdi; then
  wget -O chr-$ros_version.vdi.zip "https://download.mikrotik.com/routeros/$ros_version/chr-$ros_version.vdi.zip"
  unzip chr-$ros_version.vdi.zip
  rm chr-$ros_version.vdi.zip
fi

vm_names=$(vboxmanage list vms | awk '{print $1}' | tr -d '"')

for n in {1..2}
do
  if ! echo "$vm_names" | grep -q "router_OS_$n"; then
    cp chr-$ros_version.vdi router_OS_$n.vdi
    VBoxManage internalcommands sethduuid router_OS_$n.vdi
    VBoxManage createvm --name router_OS_$n --ostype Other_64 --register
    VBoxManage storagectl "router_OS_$n" --name "SATA Controller" --add sata --controller IntelAhci
    VBoxManage storageattach router_OS_$n \
      --storagectl "SATA Controller" \
      --device 0 \
      --port 0 \
      --type hdd \
      --medium router_OS_$n.vdi
    VBoxManage modifyvm "router_OS_$n" --memory 128 --vram 8 --nic1 bridged --bridgeadapter1 $get_interface_name --nic2 bridged --bridgeadapter2 $get_interface_name
  fi
  VBoxManage startvm "router_OS_$n"
done

if test -f chr-$ros_version.vdi.zip; then
  rm chr-$ros_version.vdi.zip
fi