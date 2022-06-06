import gdb
import sys
import json
import time

def findins() :
    print ("executing find in")
    try:

        gdb.execute("set pagination off")
        gdb.execute("gef config context.enable False")

        try:
            results = gdb.execute("x/xi $eip", to_string=True)
        except Exception:
            print("target not running right now, exiting")
            gdb.execute("quit")

        # gdb.execute("finish")
        # gdb.execute("finish")
        # #gdb.execute("finish")
        # #import pdb; pdb.set_trace()
        # while "<" in results or ("::" in results and "(" in results) :
        #     gdb.execute("finish")
        #     results = gdb.execute("x/xi $eip", to_string=True)
        #     print("\t" + results)
        #     #time.sleep(.2)

        #import pdb;pdb.set_trace()

        #
        # ut = gdb.lookup_type("uint32_t").strip_typedefs()
        # ult = ut.pointer()
        # found = False
        # instrcnt = 0
        # threshold = 10000
        # while not found:
        #
        #     for x in range(0, 8):
        #         val = gdb.Value(gdb.parse_and_eval("$eip") + x).cast(ult).dereference()
        #         #print("\t{}".format(val), end=" ")
        #         if val in goal:
        #             found = True
        #             break
        #     #print("")
        #     if found:
        #         print("\nFOUND AND STOPPING!!!!\n")
        #         break
        #
        #     _ = gdb.execute("ni", to_string=True)
        #
        #     instrcnt += 1
        #     if instrcnt == threshold:
        #         print("ran {} instructions, but couldn't find match".format(threshold))
        #         break
        #     if instrcnt % 500 == 0:
        #         print("\t#{} Search 500 instructions".format(instrcnt/500))
        #
        # with open("/tmp/data.json","r") as fp:
        #   jdata = json.load(fp)
        #
        # results = gdb.execute("x/xi $eip", to_string=True)
        # while "call" not in results:
        #     gdb.execute("ni")
        #     results = gdb.execute("x/xi $eip", to_string=True)
        #
        # #printfunc_val = gdb.Value(gdb.parse_and_eval("$eip")).cast(ult).dereference()
        # #print(hex(printfunc_val))
        #
        # #import pdb;pdb.set_trace()
        #
        # printfunc_addr = "0x" + results.split("x")[2].strip("\n")
        #
        # if printfunc_addr in jdata:
        #     print("YEEEEESSSSSSSS!!!!!!!!!!!!!!!!")
        #     print("\t\t{}".format(jdata[printfunc_addr]))
        # else:
        #     jdata[printfunc_addr] = results
        #     print(jdata)
        #     with open("/tmp/data.json", "w") as fp:
        #         json.dump(jdata, fp)

            #gdb.execute("quit")

    # gdb.execute("break *0x{:x}".format(elf.e_entry), to_string=True)
    # gdb.execute("run " + "b"*100)
    # gdb.execute("delete breakpoints 1")
    # gdb.execute("checkpoint")
    #
    # start_addr, end_addr = get_exec_range(elf.file_path)
    # random_loc_values = add_breakpoints(end_addr, elf.e_entry, elf.file_path)
    #
    # clean_workarea(outputdir)
    #
    # #################
    # # Init vars
    # #################
    # start_info = getcurrentpos(arg)
    #
    # cue = start_info["cue"]
    #
    # if not cue:
    #     for file in glob.glob(inputdir+"/*"):
    #         cue.append(file)
    #
    #     for qfile in glob.glob(WORKAREA + "/q*"):
    #         cue.append(qfile)
    #
    #     #print(cue)
    #
    # run_id = start_info["id"]
    # this_process_execs = 0
    # crashes_detected = start_info["crash_cnt"]
    # all_results=start_info["all_results"]
    #
    # output = {"max_exec_time": 0}
    # mutant_fn = ""
    # random.seed(time.time())
    # stage_starting_loc = start_info["stage_cnt"]
    # total_elapsed = timeit.default_timer()
    # exec_runtime = 0
    # first_run = True
    # try:
    #     mutators = Mutators().mutators
    #     while cue:
    #         try:
    #             cur_file = cue.popleft()
    #             Mutators.set_cue(cue)
    #             if not first_run:
    #                 stage_starting_loc = 0
    #             first_run = False
    #         except IndexError as ie:
    #             break
    #
    #         if not exists(cur_file):
    #             continue
    #
    #         cur_file_bytes = bytearray(open(cur_file, "rb").read())
    #
    #         cue_crashes = start_info["cue_crashes"]
    #         for stage_cnt in range(stage_starting_loc, len(mutators)):
    #             if stage_cnt == (len(mutators) - 1) and len(cur_file_bytes) > 3 and len(cue) > 1:
    #                 if stage.name == "SPLICED HAVOC!":
    #                     cur_file_bytes = Mutators.splice(cur_file_bytes)
    #
    #             stage = mutators[stage_cnt]
    #             #print ("stagemax={}".format(stage.calcmax(len(cur_file_bytes))))
    #             for x in range(0, stage.calcmax(len(cur_file_bytes))):
    #
    #                 #print("start x={} max={}".format(x, stage.calcmax(len(cur_file_bytes))))
    #                 savecurrentpos(arg, run_id, stage_cnt, crashes_detected, all_results, cue, cur_file, cue_crashes)
    #                 run_id += 1
    #                 this_process_execs += 1
    #                 #print("here x={}".format(x))
    #                 if x == 0:
    #                     workfile = cur_file
    #                 else:
    #                     mutant_fn = create_mutant(cur_file_bytes[:], stage.func, x-1)
    #
    #                     workfile = mutant_fn
    #
    #                 #print("workfile = " + workfile)
    #
    #                 start_time = timeit.default_timer()
    #
    #                 crash, temp_res = trace(run_id, arg, workfile, random_loc_values, outputdir)
    #                 this_exec_time = (timeit.default_timer() - start_time)
    #                 exec_runtime += this_exec_time
    #                 #total_runtime = timeit.default_timer() - total_elapsed
    #
    #                 new_result_type = update_results(all_results, temp_res, crash)
    #                 if crash:
    #                     crashes_detected += 1
    #                     cue_crashes += 1
    #
    #                 if new_result_type > 0:
    #
    #                     qfilename = WORKAREA + "/qfile-{}".format(run_id)
    #                     copyfile(workfile, qfilename)
    #                     cue.append(qfilename)
    #                     try:
    #                         chmod(qfilename, 0o777)
    #                     except Exception:
    #                         pass
    #
    #                     # new crash with uniq path
    #                     if new_result_type == 2:
    #                         crash_fn = outputdir + "/crashes/crash-{}".format(crashes_detected)
    #                         print("Creating a crash file %s" % crash_fn)
    #                         copyfile(workfile, crash_fn)
    #                         try:
    #                             chmod(crash_fn, 0o777)
    #                         except Exception:
    #                             pass
    #
    #                 output["id"] = run_id
    #                 output["fn"] = basename(cur_file)
    #                 output["eps"] = this_process_execs / exec_runtime
    #                 output["exec_runtime"] = exec_runtime
    #                 output["execs"] = this_process_execs
    #                 output["max_exec_time"] = this_exec_time if this_exec_time > output["max_exec_time"] else output["max_exec_time"]
    #                 output["unique_paths"] = len(all_results["all"])
    #                 output["crashes"] = crashes_detected
    #                 output["unique_crashes"] = len(all_results["crashes"])
    #                 output["stage"] = stage.name
    #                 output["stage_cnt"] = stage_cnt
    #                 output["mutations"] = x
    #                 output["cue_crashes"] = cue_crashes
    #                 print(json.dumps(output))
    #
    #                 if len(mutant_fn) > 0 and exists(mutant_fn):
    #                     removefile(mutant_fn)
    #                 #print("end x={}".format(x))
    #
    #         if basename(cur_file).startswith("qfile-"):
    #             removefile(cur_file)


    except gdb.error as gdberr:
        print("un caught gdb error")
        print (gdberr)
        print(sys.exc_traceback)

    except KeyboardInterrupt:
        print("\nI'm sorry to see you go.  :-(")
    except Exception as ex:
        print (ex)
        print(sys.exc_traceback)
    finally:
        #gdb.execute("set pagination on")
        gdb.execute("gef config context.enable True")

        print("\nSearch ENDED\n")
        #gdb.execute("quit")
