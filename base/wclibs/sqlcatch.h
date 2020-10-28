#ifndef cgiwrapper_h__
#define cgiwrapper_h__


//#ifdef __cplusplus
//extern "C" {
//    void webcam_trace_init(char * ch_shm_id);
//    void webcam_trace_finish();
//    void start_intercept();
//    void webcam_trace_log_op(int lineno, int opcode, char *pyfile);
//}
//#else
extern void webcam_trace_log_op(int lineno, int opcode, char *var1);
extern void webcam_trace_log_op2(int lineno, int opcode, char *var1, char *var2);
//extern void afl_forkserver(char*);
extern void webcam_trace_init(char * ch_shm_id);
extern void webcam_trace_finish();
extern void start_intercept();

//#endif

#endif  // cgiwrapper_h__